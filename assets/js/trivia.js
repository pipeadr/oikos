/**
 * Oikos — Trivia "Mitos y verdades".
 *
 * Arma el mazo sobre el HTML del servidor (que sin JS es una lista legible):
 * quita `hidden` a los controles, marca .is-ready y reparte los estados de
 * las cartas (.is-active · .is-queued con --depth 1…4 · .is-waiting ·
 * .is-gone). La animación es CSS; aquí solo cambian clases.
 *
 * - Temporizador (data-seconds; 0 = sin reloj): deadline + setInterval, no
 *   animaciones CSS (así se puede probar en headless). Arranca cuando el mazo
 *   entra en pantalla por primera vez y se pausa con la pestaña oculta o el
 *   mazo fuera de pantalla. Al llegar a 0 cuenta como respuesta incorrecta.
 * - Foco: al responder, a la respuesta correcta (título de la cara de
 *   respuesta); al continuar, a la afirmación siguiente; al terminar, al
 *   título de la carta final. La región viva anuncia el resultado y un único
 *   aviso cuando quedan 5 segundos (el conteo no se anuncia).
 * - dataLayer (GTM no ve el estado interno): trivia_start, trivia_answer,
 *   trivia_complete, trivia_restart.
 */
(() => {
	'use strict';

	const sections = document.querySelectorAll('[data-trivia]');

	if (!sections.length) {
		return;
	}

	window.dataLayer = window.dataLayer || [];

	const VISIBLE_BEHIND = 4; // = --trivia-deck-visible
	const WARNING_SECONDS = 5;

	const format = (template, value) => String(template || '').replace('%s', value);

	const initSection = (section) => {
		const deck = section.querySelector('.oikos-trivia__deck');
		const finalCard = deck && deck.querySelector('.oikos-trivia__card--final');
		const cards = deck ? Array.from(deck.querySelectorAll('.oikos-trivia__card:not(.oikos-trivia__card--final)')) : [];
		const live = section.querySelector('[data-trivia-live]');

		if (cards.length < 2 || !finalCard) {
			return;
		}

		const seconds = Math.max(0, parseInt(section.dataset.seconds, 10) || 0);
		const location = section.dataset.trackLocation || 'trivia';
		let strings = {};

		try {
			strings = JSON.parse(section.dataset.strings || '{}');
		} catch (error) {
			strings = {};
		}

		const state = {
			index: 0,
			correct: 0,
			answered: false,
			started: false,
			inView: false,
			remaining: seconds * 1000,
			deadline: 0,
			timer: 0,
			warned: false,
		};

		const push = (event, data = {}) => {
			window.dataLayer.push({ event, trivia_location: location, ...data });
		};

		const announce = (message) => {
			if (live) {
				live.textContent = message;
			}
		};

		const statementText = (card) =>
			card.querySelector('.oikos-trivia__statement').textContent.trim().replace(/^[“"]|[”"]$/g, '');

		/* ---------- Mazo ---------- */

		const STATES = ['is-active', 'is-queued', 'is-waiting', 'is-gone'];

		const setState = (card, name, depth = 0) => {
			card.classList.remove(...STATES);
			card.classList.add(name);
			card.style.setProperty('--depth', String(depth));
			card.inert = 'is-active' !== name;
		};

		/** Reparte los estados según la pregunta actual. */
		const layout = () => {
			cards.forEach((card, i) => {
				const offset = i - state.index;

				if (offset < 0) {
					setState(card, 'is-gone');
				} else if (0 === offset) {
					setState(card, 'is-active');
				} else if (offset <= VISIBLE_BEHIND) {
					setState(card, 'is-queued', offset);
				} else {
					setState(card, 'is-waiting', VISIBLE_BEHIND);
				}
			});

			// La carta final no cuenta como pregunta restante: espera invisible.
			if (state.index >= cards.length) {
				setState(finalCard, 'is-active');
			} else if (!finalCard.classList.contains('is-gone')) {
				setState(finalCard, 'is-waiting', VISIBLE_BEHIND);
			}
		};

		/* ---------- Temporizador ---------- */

		const activeCard = () => cards[state.index];

		const renderTime = (left) => {
			const card = activeCard();
			const timer = card && card.querySelector('.oikos-trivia__timer');

			if (!timer) {
				return;
			}

			timer.querySelector('.oikos-trivia__seconds').textContent = `${left}s`;
			timer.classList.toggle('is-ending', seconds > WARNING_SECONDS && left <= WARNING_SECONDS);
		};

		const stopTimer = () => {
			window.clearInterval(state.timer);
			state.timer = 0;
		};

		const tick = () => {
			const left = Math.max(0, state.deadline - performance.now());
			const shown = Math.ceil(left / 1000);

			state.remaining = left;
			renderTime(shown);

			if (!state.warned && seconds > WARNING_SECONDS && shown <= WARNING_SECONDS && left > 0) {
				state.warned = true;
				announce(strings.ending || '');
			}

			if (left <= 0) {
				answer(null);
			}
		};

		const startTimer = () => {
			if (!seconds || state.timer || state.answered || state.index >= cards.length || !state.inView || document.hidden) {
				return;
			}

			state.deadline = performance.now() + state.remaining;
			state.timer = window.setInterval(tick, 250);
		};

		const pauseTimer = () => {
			if (!state.timer) {
				return;
			}

			stopTimer();
			state.remaining = Math.max(0, state.deadline - performance.now());
		};

		const resetTimer = () => {
			stopTimer();
			state.remaining = seconds * 1000;
			state.warned = false;
			renderTime(seconds);
		};

		const ensureStarted = () => {
			if (!state.started) {
				state.started = true;
				push('trivia_start');
			}
		};

		/* ---------- Flujo ---------- */

		/** choice = 'verdad' | 'mito'; null = se acabó el tiempo. */
		function answer(choice) {
			const card = activeCard();

			if (state.answered || !card) {
				return;
			}

			ensureStarted();
			stopTimer();
			state.answered = true;

			const correctAnswer = card.dataset.answer;
			const timedOut = null === choice;
			const isCorrect = !timedOut && choice === correctAnswer;
			let key = 'incorrect';

			if (timedOut) {
				key = 'timeout';
			} else if (isCorrect) {
				key = 'correct';
				state.correct += 1;
			}

			card.querySelector('.oikos-trivia__timeout').hidden = !timedOut;
			card.classList.add('is-answered', isCorrect ? 'is-correct' : 'is-incorrect');
			card.querySelector('.oikos-trivia__verdict').focus();
			announce(format(strings[key], strings[correctAnswer] || correctAnswer));

			push('trivia_answer', {
				question_number: state.index + 1,
				question_text: statementText(card),
				answer_given: timedOut ? '' : choice,
				is_correct: isCorrect,
				timed_out: timedOut,
			});
		}

		const next = () => {
			if (!state.answered) {
				return;
			}

			state.index += 1;
			state.answered = false;
			layout();

			if (state.index >= cards.length) {
				finalCard.querySelector('.oikos-trivia__final-title').focus();
				push('trivia_complete', {
					correct_total: state.correct,
					questions_total: cards.length,
				});
				return;
			}

			activeCard().querySelector('.oikos-trivia__statement').focus();
			resetTimer();
			startTimer();
		};

		const restart = () => {
			stopTimer();
			state.index = 0;
			state.correct = 0;
			state.answered = false;

			// Las preguntas vuelven al mazo sin animar; la carta final sale hacia la izquierda.
			section.classList.add('is-resetting');
			cards.forEach((card) => {
				card.classList.remove('is-answered', 'is-correct', 'is-incorrect');
				card.querySelector('.oikos-trivia__timeout').hidden = true;
			});
			setState(finalCard, 'is-gone');
			layout();
			void deck.offsetWidth; // aplica el estado sin transición antes de reactivarlas
			section.classList.remove('is-resetting');

			announce('');
			activeCard().querySelector('.oikos-trivia__statement').focus();
			push('trivia_restart');
			resetTimer();
			startTimer();
		};

		/* ---------- Eventos ---------- */

		deck.addEventListener('click', (event) => {
			const button = event.target.closest('button');

			if (!button || !deck.contains(button)) {
				return;
			}

			if (button.classList.contains('oikos-trivia__choice')) {
				answer(button.dataset.choice);
			} else if (button.classList.contains('oikos-trivia__next')) {
				next();
			} else if (button.classList.contains('oikos-trivia__restart')) {
				restart();
			}
		});

		document.addEventListener('visibilitychange', () => {
			if (document.hidden) {
				pauseTimer();
			} else {
				startTimer();
			}
		});

		if ('IntersectionObserver' in window) {
			new IntersectionObserver(
				(entries) => {
					entries.forEach((entry) => {
						state.inView = entry.isIntersecting;

						if (state.inView) {
							ensureStarted();
							startTimer();
						} else {
							pauseTimer();
						}
					});
				},
				{ threshold: 0.5 }
			).observe(deck);
		} else {
			state.inView = true;
		}

		/* ---------- Arranque ---------- */

		section.querySelectorAll('.oikos-trivia__meta, .oikos-trivia__choices, .oikos-trivia__next').forEach((el) => {
			el.hidden = false;
		});
		finalCard.hidden = false;
		section.classList.add('is-resetting', 'is-ready'); // el primer reparto no anima
		layout();
		void deck.offsetWidth;
		section.classList.remove('is-resetting');
		renderTime(seconds);

		if (!('IntersectionObserver' in window)) {
			ensureStarted();
			startTimer();
		}
	};

	sections.forEach(initSection);
})();
