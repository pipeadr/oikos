# CLAUDE.md — Tema "oikos"

Tema WordPress a la medida para **Oikos Psicología** (terapia psicológica individual, de pareja y de familia; presencial en Medellín y virtual). Desarrollador: Pipe (Andrés Acevedo).

## Prioridades del proyecto (en este orden)
1. **Fidelidad al diseño** (Figma) en desktop.
2. **Responsive impecable** en tablet y móvil, aunque no exista diseño para esa vista. Nunca entregar una sección sin revisarla en 375px, 768px, 1024px y 1440px.
3. **SEO técnico** (es prioridad del cliente).
4. **Rendimiento** (Core Web Vitals en verde en móvil).
5. **Todo administrable**: ningún texto, imagen o enlace visible quemado en el código.

## Stack y reglas técnicas
- WordPress + tema clásico propio (PHP), **sin Elementor ni constructores**.
- **ACF Pro** para todo el contenido editable. **ACF Local JSON activado** en `/acf-json` (los grupos de campos viajan en git).
- **CSS vanilla** con variables (custom properties). Sin Sass, sin Tailwind, sin paso de build.
- **JS vanilla** (ES6+), sin jQuery. Cargar con `defer` y solo en las plantillas que lo necesiten.
- **Contact Form 7** para formularios.
- **Yoast SEO** para metas, sitemap, breadcrumbs y el grafo base de schema; el schema específico del negocio lo agrega el tema **extendiendo el grafo de Yoast** (ver SEO).
- **GTM** instalado desde el inicio; GA4 se configura dentro de GTM.
- Idioma: **solo español**, pero todos los strings del tema con funciones de traducción (`__()`, `esc_html_e()`…, text domain `oikos`).
- Prefijos: funciones/hooks PHP `oikos_`, clases CSS `oikos-` con BEM (`oikos-card__title--active`), handles de assets `oikos-`.
- Escapar toda salida (`esc_html`, `esc_url`, `esc_attr`, `wp_kses_post`). Sanitizar toda entrada.

## Entornos y despliegue
- **Local:** WAMP (`C:\wamp64\www\oikos`). El tema vive en `wp-content/themes/oikos` y es el repo git.
- **Repo:** `github.com/pipeadr/oikos` — solo contiene el tema (no core, no plugins, no uploads).
- **Ramas:** `main` (producción, futuro) · `develop` (pre-producción) · `feature/<nombre>` para cada componente o página.
- **Pre-producción:** Hostinger, despliegue automático desde `develop` con la integración Git de Hostinger.
- Flujo: cambio en local → commit → push a `develop` → Hostinger despliega el tema solo.
- **Git solo mueve el código del tema** (incluidos los campos ACF en `acf-json`). Páginas, textos, imágenes, menús y ajustes viven en la base de datos y en `uploads`, que **no** viajan por git.
- El contenido real se carga en pre-producción. **Nunca** subir la base de datos local al servidor; si se necesita contenido real en local, bajarlo con un plugin de migración.

## Estructura del tema
```
oikos/
├── style.css            # solo cabecera del tema
├── functions.php        # solo requires de /inc
├── inc/                 # setup, enqueue, cpt, taxonomías, acf (options pages), schema, helpers, ajax
├── templates/           # plantillas de página (Template Name)
├── template-parts/
│   ├── layout/          # header, footer, newsletter
│   ├── sections/        # secciones completas reutilizables
│   └── components/      # piezas pequeñas (botón, card, tag, acordeón…)
├── assets/
│   ├── css/             # base.css (tokens, reset, tipografía) + un archivo por componente/plantilla
│   ├── js/
│   ├── fonts/           # fuentes auto-hospedadas (woff2)
│   └── img/             # solo íconos/decorativos del tema; las fotos van por la biblioteca de medios
└── acf-json/
```

## Design tokens
Definidos en `assets/css/base.css`. Los nombres siguen los tokens del Figma: `grupo/rol` → `--grupo-rol` (ej. `color/text/primary` → `--color-text-primary`). **Nunca usar hex, px de espaciado o tamaños de fuente sueltos en un componente: siempre los tokens.** Para revisarlos: página privada **"Guía de estilos"** (plantilla `templates/style-guide.php`; solo editores, `noindex`). En cada entorno hay que crearla a mano como página Privada con esa plantilla.

### Colores
| Token Figma | Variable CSS | Valor |
|---|---|---|
| color/text/primary | `--color-text-primary` | #212121 |
| color/text/secondary | `--color-text-secondary` | #4B3A2F |
| color/interactive/primary | `--color-interactive-primary` | #2D5545 |
| color/interactive/secondary | `--color-interactive-secondary` | #8E5325 |
| color/text/brand | `--color-text-brand` | = interactive/primary (#2D5545). Beneficio activo de "¿Por qué asistir?" |
| color/content/inverse | `--color-content-inverse` | #FBF5E9 |
| color/surface/base | `--color-surface-base` | #FBF5E9 |
| color/feedback/surface/base | `--color-surface-muted` | #EAEAEA (banda "Cómo iniciar" de Servicios; descripción text/secondary ≈ 9:1) |
| color/border/default | `--color-border-default` | #212121 al 16 % |
| trivia: acertó · falló/tiempo · final (medidos en captura) | `--color-trivia-correct` · `--color-trivia-incorrect` · `--color-trivia-final` | #2D671C (6.3:1) · #67110A (11.7:1) · #1B478A (8.4:1), contraste con texto content/inverse |
| *(por confirmar)* fondo de página | `--color-surface-page` | #FFFFFF |
| marca, sin rol | `--color-brand-sage` · `--color-brand-sand` | #80957D · #D1A980 |
| derivado (≥ 3:1 sobre blanco, WCAG 1.4.11) | `--color-brand-sand-strong` | #B6875C = 60 % sand / 40 % interactive/secondary (3.18:1). Indicadores inactivos del slider sobre fondo claro (sand puro da 2.16:1) |
| color/decorative/caramel | `--color-decorative-caramel` | #A87A53 |
| ícono WhatsApp (medido en captura) | `--color-brand-whatsapp` | #65D072 (1.94:1 sobre blanco: solo como ícono de apoyo junto a texto que nombra el enlace) |
| derivado (AA con texto crema) | `--color-brand-caramel-strong` | #926641 (4.61:1) |
| gradiente de bandas | `--gradient-brand` | interactive/primary → mezcla 50 % con sage (#567561, 4.7:1). Sage puro con texto crema da 2.97:1: no usarlo bajo texto |
| derivado (AA con texto crema) | `--color-brand-sage-strong` | #587662 = mezcla 52 % sage / 48 % verde (4.62:1). Etiqueta "Terapia familiar" |

Superficies sobre foto (sección Nosotros): `--color-surface-glass` (vidrio verdoso: mezcla verde/sage al 35 % + `backdrop-filter: blur(16px)`; título text/primary, descripción blanca surface/page), `--color-border-glass` (blanco 20 %) y `--overlay-photo` (velo verde al 30 %). Medido en el render: descripción ≥ 4.8:1, título crema de la sección ≥ 4.3:1, título oscuro de tarjeta 2.6–3.4:1 (excepción, ver Accesibilidad). Oscurecer el vidrio empeora el título; aclararlo rompe la descripción.

Banda newsletter: `--gradient-newsletter` (radial del Figma, `50% 212.08% at 50% 50%`, verde → **`--color-brand-sage-strong`** en lugar de sage puro: con sage el lema daba 3.6:1 y los bordes a 375 px 3:1; así el mínimo es 4.62:1). Formularios: `--color-feedback-error` (#B3261E, *deducido*, 6:1 sobre crema), `--color-feedback-success` (= interactive/primary), `--field-height` (48, medido), `--text-tagline-size` (16 → 18).

Otros tokens: `--shadow-float` (barras y desplegables flotantes), `--shadow-lift` y `--transition-lift` (tarjetas que se elevan al hover), `--z-header`, `--header-top`, `--header-bar-height`, `--header-offset` (espacio que ocupa el header fijo; `main` lo usa como padding superior), `--hero-split-top` (24 → 44: lo que el hero foto + texto suma sobre `--header-offset` para quedar a 176 como el Figma), `--transition-card-zoom` (450 ms ease-out: tarjeta de servicio), `--transition-color` (300 ms: color de texto) y `--transition-crossfade` (500 ms: fundido de fotos). Fondo de "¿Por qué asistir?": `--gradient-benefits` (radial arena arriba a la derecha sobre crema; el linear sólido que exporta Figma va **debajo**, como color de fondo, porque Figma apila las capas al revés que CSS). Fondo de "Todo lo que puedes trabajar…": `--gradient-topics` (radial arena abajo a la derecha sobre crema). `--transition-expand` (350 ms ease-out: tarjeta de tema que se abre), `--duration-deck` / `--transition-deck` (400 ms ease-out: cartas de la trivia; la duración va aparte porque también es el retraso del `visibility` al ocultar) y `--drop-shadow-lift` / `--drop-shadow-hover` (la elevación como `filter`, para elementos con `clip-path`, donde `box-shadow` no se ve).

### Tipografía
- **Bricolage Grotesque** (`--font-heading`, solo peso 500, con eje de tamaño óptico `opsz`) y **Figtree** (`--font-body`, variable 400–700; el 700 lo usan las tarjetas del hero). Auto-hospedadas en `assets/fonts` (woff2, subset latin, licencias OFL incluidas), `font-display: swap`, ambas precargadas (`oikos_preload_fonts()` en `inc/enqueue.php`).
- Estilos como **clases independientes del nivel de encabezado** (un `h2` puede usar `.oikos-text-display`). Sin clase, `h1` = Display, `h2` = Heading, `h3–h6` = Card.

| Estilo Figma | Clase | Fuente | Tamaño móvil → desktop | Line-height | Tracking |
|---|---|---|---|---|---|
| Display | `.oikos-text-display` | Bricolage 500 | 38 → 64 | 110 % | -2 % |
| Display S *(deducido)* | `.oikos-text-display-sm` (`--text-display-sm-size`) | Bricolage 500 | 32 → 40 | 120 % | -2 % |
| Heading | `.oikos-text-heading` | Bricolage 500 | 26 → 32 | 125 % | -1.5 % |
| Body | `.oikos-text-body` | Figtree 400 | 16 | 150 % | 0 |
| Button | `.oikos-text-button` | Figtree 500 | 16 | 140 % | 2 % |
| Card *(deducido)* | `.oikos-text-card` | Figtree 600 | 18 → 20 | 125 % | 0 |
| Card hero | `.oikos-text-card-hero` (`--text-card-hero-size`) | Figtree **700** | 20 → 24 | 100 % | 0 |
| Card service | `.oikos-text-card-service` (`--text-card-service-size`) | Bricolage 500 | 20 → 24 | 133 % | -1 % |
| Título de entrada *(deducido)* | `.oikos-post-card__title` (`--text-post-title-size`) | Figtree 500 | 18 → 20 | 140 % | 0 |
| Etiqueta | `.oikos-tag` | Figtree 500 | 14 | 100 % | 0 |
| Step *(deducido)* | `.oikos-text-step` | Bricolage 500 | 28 → 48 | 110 % | -2 % |
| Heading L (Figma: Typography/Heading…) | `.oikos-text-heading-lg` (`--text-heading-lg-size`) | Bricolage 500 | 28 → 48 | 120 % | -2.5 % |
| Eyebrow *(deducido)* | `.oikos-text-eyebrow` | Figtree 500 | 14 | 140 % | 0 |
| Nav *(deducido)* | `.oikos-text-nav` | Figtree 500 | 14 | 140 % | 0 |

Modificadores de color: `.oikos-text-secondary`, `.oikos-text-inverse`.

### Espaciado, radios y layout
- Escala de 8 px: `spacing/N` → `--spacing-N` (`--spacing-1` = 8 … `--spacing-10` = 80).
- Radios: `--radius-xs` = 4 px (indicadores del slider, *deducido*), `--radius-sm` = 8 px (campos de formulario, medido en captura), `--radius-md` = 12 px (botones, *deducido*), `--radius-lg` = 16 px (tarjetas), `--radius-xl` = 24 px (tarjetas de temas, medido en captura), `--radius-full` (píldoras/etiquetas). `--tag-padding-block` = 10 px (alto de etiqueta ≈ 36, *deducido*).
- Bordes: `--border-width-thick` = 2 px (*deducido*).
- Contenedor: `.oikos-container` → ancho máximo `--container-max` (1110 px) con `--container-padding` a cada lado. En 1366 deja 128 px por lado, como el Figma.
- Sección: `.oikos-section` → `padding-block: var(--space-section)`.

### Componentes base
- **Botón** (`assets/css/button.css`): `.oikos-btn` + `--primary` (fondo interactive/primary), `--secondary` (interactive/secondary), `--light` (surface/base, texto text/primary; no usar sobre fondo crema porque no se ve). Alto 54 px, padding spacing/2 × spacing/3, `--radius-md`, estilo Button. Hover oscurece, active oscurece más y baja 1 px, focus-visible con doble anillo.
- Variantes extra: `--tonal` (caramelo oscurecido con borde crema, **solo sobre el café** interactive/secondary), `--outline-light` (transparente con borde y texto content/inverse; **solo sobre fotos o fondos oscuros**; el borde es `box-shadow: inset` para mantener los 54 px) y `--compact` (44 px de alto, CTA del header).
- **Tarjeta** (`assets/css/card.css`): `.oikos-card` → surface/base, `--card-padding`, gap spacing/2, `--radius-lg`, borde 1 px border/default.

### Header y footer
- Header fijo y **siempre visible** (`template-parts/layout/site-header.php`, `header.css`, `header.js`). Arriba del todo la barra es plana (sobre el hero sin borde; en páginas internas con borde border/default); al dejar el inicio recibe `.is-scrolled` y lleva `--shadow-float` (lo detecta un `IntersectionObserver` sobre un centinela, sin listener de scroll). Desktop ≥ 1024 con píldoras; < 1024 hamburguesa con panel dentro de la barra; < 360 el CTA pasa al panel. En pantallas bajas (`max-height: 500px`, teléfono horizontal) se reducen `--header-top` y `--header-bar-height`.
- Compensación del header fijo: `main` lleva `padding-top: var(--header-offset)` (salvo con hero) y `html` lleva `scroll-padding-top: var(--header-offset)` (más la barra de administración de WP). Se usa `scroll-padding-top` y **no** `scroll-margin-top` porque además evita que el foco de teclado quede bajo la barra (WCAG 2.4.11). No combinar los dos: se suman.
- Plantillas con hero a sangre: `add_filter( 'oikos_has_hero', '__return_true' );` → body `oikos-has-hero` y `main` sin padding superior (el header flota sobre la foto).
- Menú principal: walker `Oikos_Primary_Nav_Walker` (`inc/nav.php`). Un ítem con hijos se vuelve `<button>` (su URL no se usa); el primer hijo debe ser la página general.
- Columnas del footer: el título es el **nombre del menú** asignado. Ubicaciones: `primary`, `footer-acompanamientos`, `footer-contenido`, `footer-oikos`, `footer-legal`.
- Logos: `template-parts/components/logo.php` + `oikos_logo_img()`. Acepta SVG sin width/height (el alto lo da el CSS); alt de la biblioteca o, si falta, `oikos_global_marca_nombre`. Variantes: `default` (custom logo, header), `inverse` ("Logo para fondos oscuros", 40 → 56; hoy solo es respaldo, el bloque café no muestra logo) y `newsletter` ("Logo del newsletter" `oikos_global_newsletter_logo`, versión árbol + "OIKOS Psicología"; vacío → logo para fondos oscuros → nombre del sitio; clases `--inverse --newsletter`, 96 → 158 de alto, máx. 344 de ancho).
- Íconos: `oikos_the_icon( 'nombre' )` con SVG de `assets/img/icons/`. Ilustraciones decorativas: `oikos_the_decor( 'decor-lineas', 'clase' )` (SVG en línea de `assets/img/`, `aria-hidden`, tamaño por CSS).
- Botones que caen al CTA global si el campo está vacío: `oikos_link_or_cta( $link )`.
- **Newsletter** (`template-parts/layout/newsletter.php`, estilos en `footer.css`): logo del newsletter (variante `newsletter` del componente logo, ver arriba) + lema a la izquierda (columna de 345 px, gap 64); título (estilo Heading), descripción y formulario CF7 a la derecha. < 1024: marca arriba y todo centrado (contenido máx. 700 px); < 768: campo y botón apilados a ancho completo. El formulario se elige en "Oikos — Global" → pestaña **Newsletter** (`oikos_global_newsletter_logo`, `_formulario`, `_titulo` y `_texto`); sin formulario solo se ven los textos. Plantilla del formulario CF7 (copiarla igual en cada entorno):
  ```
  <div class="oikos-form__row">
  <label class="oikos-form__field"><span class="screen-reader-text">Correo electrónico</span>[email* correo autocomplete:email placeholder "correo@correo.com"]</label>
  [submit "Suscribirme"]
  </div>
  <div class="oikos-form__consent">[acceptance autorizacion]Acepto la <a href="/politica-de-tratamiento-de-datos/">política de tratamiento de datos</a>.[/acceptance]</div>
  ```
  Configuración adicional: `acceptance_as_validation: on` (sin aceptar la casilla muestra un error en vez de deshabilitar el botón). Las suscripciones quedan en **CFDB7**; el envío a una plataforma externa sigue pendiente.

### Responsive
No hay diseño móvil ni tablet. **El valor del Figma es el máximo (desktop, frame de 1366); el mínimo es para 375.** Entre ambos se interpola en línea recta con `clamp()`. Los componentes usan estos tokens y no definen sus propios valores por breakpoint.
- Anchos de referencia y de revisión: **375 · 768 · 1024 · 1366** (y 1440). Breakpoint para cambios de layout en componentes: `max-width: 47.9375rem` (< 768) para móvil y `64rem` (1024) para laptop.

| Token | Móvil (375) | Desktop (1366+) |
|---|---|---|
| `--container-padding` | 20 | 80 |
| `--space-section` | 48 | 80 |
| `--card-padding` | 20 | 24 |
| Display / Heading / Card | 38 / 26 / 18 | 64 / 32 / 20 |
| Card hero / Card service | 20 / 20 | 24 / 24 |
| Body / Button / Eyebrow / Nav | fijos: 16 / 16 / 14 / 14 (el texto de lectura nunca baja de 16) | |

- **Botones en móvil:** mantienen 54 px de alto (más que los 44 px táctiles mínimos). **No van a ancho completo por defecto**, porque en el diseño son cortos y alineados a la izquierda, y en tarjetas o junto a otros elementos el ancho completo rompe la composición. El CTA principal de una sección (hero, bandas CTA) usa `.oikos-btn--block-mobile` (100 % por debajo de 768 px).
- Cualquier elemento interactivo pequeño (píldoras del menú, íconos) debe medir mínimo 44 × 44 px en móvil.
- Nada puede desbordar horizontalmente en 375 px.

### Estética general
Bordes redondeados generosos, tarjetas crema sobre fondos crema/blanco, gradientes verde→sage en bandas destacadas, ilustraciones de líneas orgánicas como decoración (SVG, `aria-hidden`).

## Mapa del sitio, plantillas y URLs
| Página | URL | Plantilla |
|---|---|---|
| Inicio | `/` | `front-page.php` |
| Servicios (general) | `/servicios/` | Servicios general (`templates/servicios.php`) |
| Terapia de pareja | `/servicios/terapia-de-pareja/` | **Servicio** (compartida) |
| Terapia individual | `/servicios/terapia-individual/` | **Servicio** (compartida) |
| Terapia familiar | `/servicios/terapia-familiar/` | **Servicio** (compartida) |
| El método (curso para parejas) | `/el-metodo/` | Landing El Método |
| Nosotros | `/nosotros/` | Nosotros |
| Blog | `/blog/` | `home.php` (página de entradas) |
| Entrada del blog | `/blog/<slug>/` | `single.php` |
| Contacto | `/contacto/` | Contacto |
| Agendamiento | `/agenda-tu-cita/` | Agendamiento (**sin header/footer estándar**) |

Las 3 páginas de terapia (pareja, individual, familiar) usan **la misma plantilla** con distinto contenido.

## Contenido: CPTs, taxonomías y opciones
- **CPT `equipo`** (`inc/cpt.php`; psicólogos: Paula, Juan David): nombre = título, foto = imagen destacada, campos `oikos_equipo_enfoque`, `oikos_equipo_bio_corta`, `oikos_equipo_bio_larga`. Se usa en servicios, nosotros, páginas de terapia y como **autores del blog**. **Sin páginas públicas por ahora** (no consultable, fuera del sitemap de Yoast); para activarlas: `add_filter( 'oikos_equipo_public', '__return_true' )` + guardar Enlaces permanentes (URL `/equipo/<slug>/`, y el `Person` del schema gana `url`).
- **Autores de una entrada:** campo `oikos_entrada_autores` (relación con equipo, varios, en orden). Vacío → autor de WordPress. Helpers en `inc/blog.php`: `oikos_post_authors()`, `oikos_post_team_authors()`.
- **Categorías del blog:** Terapia de pareja, Terapia individual, Terapia familiar. Color de la etiqueta por categoría: campo `oikos_categoria_color` (`crema` | `arena` | `sage`). Categoría mostrada: la principal de Yoast o la primera, ignorando "Sin categoría" (`oikos_post_category()`).
- **Página de opciones ACF "Oikos — Global":** contacto (teléfono, WhatsApp, correo, dirección), redes sociales, textos y enlaces del CTA "Agenda tu cita", los 3 badges ("Psicólogos especializados…", "100% confidencial…", "Presencial en Medellín…"), textos del bloque newsletter, banner de auto-pauta del blog (300×600). WhatsApp: número `oikos_global_whatsapp`, mensaje predeterminado `oikos_global_whatsapp_mensaje` (texto plano, sin `<br>`) y frase del bloque "¿Tienes dudas?" `oikos_global_whatsapp_cta_texto`; la URL la arma `oikos_whatsapp_url()`. Pestaña **Blog**: interruptor `oikos_global_blog_mostrar_autores` ("Mostrar autores en las tarjetas del blog", **apagado por defecto**; helper `oikos_show_post_authors()`, filtrable con `oikos_show_post_authors`). Apagado, las tarjetas no imprimen la línea de autores (sin hueco); los autores se siguen guardando en cada entrada y siguen en el schema de Yoast.
- **Testimonios:** en el Home serán las reseñas de Google del negocio (ver Pendientes).
- **Preguntas frecuentes:** grupo ACF "Preguntas frecuentes" (`group_oikos_faq`, en toda página, incluida la página de entradas): `oikos_faq_titulo`, `oikos_faq_preguntas` (repetidor `pregunta` + `respuesta` wysiwyg básico) y `oikos_faq_boton` (vacío → CTA global). Cada página tiene sus propias preguntas; sin preguntas no hay sección, ni assets, ni schema. Siempre generan schema `FAQPage`.

## Componentes reutilizables (construir una vez)
- **Header** (nav flotante crema con píldoras + CTA) y **footer** (banda newsletter verde + bloque café con CTA, menús, contacto, redes). Menús administrables desde Apariencia → Menús.
- **Hero** en sus variantes (foto a pantalla completa con texto; foto + texto en dos columnas; foto en B/N con texto).
- **Hero foto + texto** (`template-parts/sections/hero-split.php` + `hero-split.css`; páginas internas, hoy Servicios general): foto cuadrada (`oikos-square`, LCP vía `oikos_hero_picture()` con `size`/`sizes` propios) con `--radius-lg` a la izquierda y H1 `.oikos-text-display-sm` + Body text/secondary + botón primario (`data-track="agenda-cita"`) a la derecha, centrado en vertical. No activa `oikos_has_hero`: el header queda en modo página interna. Desktop: texto fijo de 411 px y la foto toma el resto (643 en 1366), gap spacing/7, spacing/4 debajo; tablet 2:3 (foto más angosta) para mantener H1 y CTA en el primer pantallazo; < 768 foto 4:3 arriba y botón a ancho completo. Sin foto → solo texto. Al usarlo en otra plantilla, agregarla a la condición de `oikos-hero-split` en `oikos_template_assets()`.
  - Grupo ACF "Servicios general" (`group_oikos_servicios`, una pestaña por sección): `oikos_servicios_hero_imagen`, `_titulo` (vacío → título de la página), `_texto`, `_boton` (vacío → CTA global). Pestaña **Acompañamientos**: `oikos_servicios_acompanamientos_titulo` (vacío → sin sección), `_texto`, `_servicios` (relación ordenable, limitada a las hijas; vacía → hijas por orden de página) y `_boton_texto` ("Pide tu cita"; va al CTA global). El orden y el texto del botón valen para **todos** los carruseles de servicios.
- **Beneficios con foto que cambia** ("¿Por qué asistir a terapia?", Servicios general): `template-parts/sections/benefits.php` + `benefits.css` + `benefits.js`. Args `id`, `title`, `heading_level`, `items` (`title`, `text`, `image_id`, `focus_x`, `focus_y`), `autoplay`, `interval`. H2 con estilo Heading (medido ≈ 355 px en el Figma; es más pequeño que los beneficios pero es el título de la sección) a la izquierda y foto vertical a la derecha; cada beneficio: número `aria-hidden` + `h3 > button` (estilo Heading L, `aria-current` en el activo, `aria-controls` → la foto) + descripción Body text/secondary (Figma ≈ 14; se usa 16 por la regla de lectura). Activo = título `--color-text-brand` con `--transition-color`; el `::after` del botón cubre todo el beneficio y lleva el anillo de foco.
  - **Fotos:** todas en el HTML, absolutas y apiladas en un marco con `aspect-ratio` (523/680 desktop, 4/5 móvil; el alto lo da el marco, no la foto), fundido con `--transition-crossfade`. Tamaños `oikos-portrait` (1046 × 1360) y `oikos-portrait-sm` (640 × 832); la primera sin lazy, el resto `lazy`; `object-position` = punto focal. Beneficio sin foto → `data-photo` apunta a la del anterior; primero sin foto → superficie arena; ninguna → `--no-media` (una columna).
  - **Rotación** (`benefits.js`): temporizador de `data-interval` s que guarda el tiempo restante al pausar. Pausan el mouse (solo `pointerType: mouse`), el foco **de teclado** dentro (`:focus-visible`; el foco por clic ya lo cubre la elección del beneficio) y la sección fuera de pantalla (`IntersectionObserver` sin umbral y con `rootMargin` -15 %: en móvil la sección es más alta que la pantalla). Elegir un beneficio la detiene para siempre. No corre con `prefers-reduced-motion`. **Sin botón de pausa:** se quitó a pedido del cliente (2026-11-16); la pausa queda cubierta por hover, foco de teclado y `prefers-reduced-motion`, y el mecanismo de parada de WCAG 2.2.2 es elegir un beneficio (clic, Enter o Espacio). No volver a agregar un control visible sin consultarlo. Línea de progreso: animación CSS de la misma duración, congelada con `.is-paused`; su espacio está reservado en todos los ítems. Sin `aria-live`. **No se usa `animationend` para avanzar:** el temporizador es independiente del CSS y se puede probar en headless (que no avanza animaciones CSS con tiempo virtual).
  - Responsive: tablet (768–1023) dos columnas 3:2 con la foto sticky (a 768 el título más largo queda en 2 líneas); < 768 título → foto 4:5 → lista.
  - Campos (pestaña **Por qué asistir** del grupo "Servicios general"): `oikos_servicios_porque_titulo` (vacío → sin sección), `_beneficios` (repetidor 2–6: `titulo`, `descripcion`, `imagen`, `foco_x`, `foco_y`), `_rotacion` (activada por defecto; se lee con `get_field()` porque `oikos_field()` trata `false` como vacío) e `_intervalo` (4–20 s, 7 por defecto).
- **Grilla de temas que se abren** ("Todo lo que puedes trabajar en un proceso terapéutico", Servicios general): `template-parts/sections/topics.php` + `topics.css` + `topics.js`. Args `id`, `title`, `text`, `heading_level`, `items` (`title`, `text`, `link` = arreglo de link de ACF), `track_location` (`data-track="tema-leer-mas"` en el enlace). Intro (Heading + Body text/secondary, máx. 312 px para cortar como el Figma) y grilla de tarjetas arena (`--radius-xl`, 162 × 173 en desktop = `aspect-ratio`, gap 24) con el título abajo (Figtree 500 `--text-card-size`, lh 150 %, `text-wrap: wrap` para cortar "Relaciones de / pareja").
  - **Apertura:** cada `li` es una celda fija; la tarjeta es absoluta, siempre mide **2 celdas sin el gap** (324: la vecina se asoma 24 px por detrás, decisión de diseño) y cerrada se recorta a su celda con `clip-path`; abrir anima el `clip-path`, así que la grilla no se mueve. El contenido va anclado abajo: la descripción entra con `grid-template-rows` 0fr → 1fr (el título sube) y fundido; su ancho ya es el de la tarjeta abierta (no se reacomoda). Cerrada: `visibility: hidden` (fuera del árbol de accesibilidad y del Tab), pero siempre en el HTML. Abierta: `--drop-shadow-lift` en el `li` y borde crema translúcido.
  - **Dirección:** la última columna se ancla a la derecha y se abre a la izquierda (contenido corrido una celda con `transform` mientras está cerrada; la tarjeta lleva `overflow: clip` para que eso no genere desplazamiento horizontal). La columna real sale de `:nth-child(4n)` / `(2n)` dentro de la query de columnas: sirve para cualquier cantidad de temas.
  - **Columnas:** container query en `.oikos-topics__cards`: 2 columnas; 4 desde 42rem (tarjetas ≥ 150; a 768 ya son 4). Intro y grilla lado a lado solo desde 72rem (1152): a 1024 las tarjetas quedarían en ~130 y los títulos en 3 líneas, así que hasta ahí va apilado (intro arriba).
  - **Interacción** (`topics.js`): `h3 > button` con `aria-expanded` / `aria-controls`; el `::after` del botón cubre la tarjeta. Una abierta a la vez; se cierra presionándola, abriendo otra, con clic fuera o con Esc (el foco vuelve al botón). `.is-closing` mantiene la que se cierra por encima mientras anima. Hover (solo mouse) eleva 4 px sin abrir. Foco: anillo en la celda (cerrada) o en la tarjeta (abierta; el `clip-path` abierto se agranda 4 px para no recortarlo). Si una descripción no cabe, la tarjeta crece hacia abajo (`min-height`); en el campo se piden ≤ 110 caracteres.
  - **Sin JS** (`@media (scripting: none)`): el servidor manda `aria-expanded="true"` y la grilla pasa a 2 columnas (1 en móvil) con todas las descripciones visibles. Tema sin descripción ni enlace → solo título, sin botón.
  - Campos (pestaña **Temas** del grupo "Servicios general"): `oikos_servicios_temas_titulo` (vacío → sin sección), `_texto` y `_lista` (repetidor 2–12: `titulo`, `descripcion`, `enlace` opcional; sin texto de enlace dice "Leer más").
- **Pasos numerados** ("Cómo funciona"/"Así es una sesión", 3 pasos): `template-parts/sections/steps.php` + `steps.css`. Args `layout`, `background` (`none` | `cream` | `white` | `muted`), `title`, `steps`, `link` (opcional) y `track`/`track_location`. Nuevas disposiciones = agregar a la lista blanca del PHP y un modificador `--layout-<nombre>`.
  - `split` (Inicio): intro a la izquierda, pasos a la derecha.
  - `rows` (Servicios general, "Cómo iniciar tu proceso paso a paso", fondo `muted`): H2 Heading centrado; cada paso es una fila `__head` (número eyebrow centrado en vertical con el título Step, gap 16) | descripción Body text/secondary, columnas 1fr 1fr con gap spacing/9 (en 1366 el título arranca en x 162 y la descripción en 719, como el Figma). Padding spacing/4 arriba y spacing/6 abajo, gap spacing/4. Título con `text-wrap: wrap` (corta "Creamos las bases de / nuestro encuentro"). Dos columnas por container query sobre la lista desde 60rem (el título más largo cabe en 2 líneas desde ~58rem); por debajo, apilado (número + título, descripción debajo, máx. 36rem): a 1024 ya va apilado. Solo en `rows` el PHP agrupa número y título en `__head`; `split` no cambia.
  - Campos (pestaña **Pasos** del grupo "Servicios general"): `oikos_servicios_pasos_titulo` (vacío → sin sección), `_lista` (repetidor 2–6: `titulo`, `texto`) y `_boton` (opcional; **vacío → sin botón**, no cae al CTA global; `data-track-location="servicios-pasos"`).
- **Tarjetas con descripción revelable** (tarjetas del hero y de Acompañamiento, modificador `--reveal`): campo "Descripción" del repetidor; si está vacío no hay cambio. Hover (solo `(hover: hover)`) o foco → fondo text/secondary, el título sube y se desvanece y la descripción entra desde abajo (`--transition-reveal`; sin movimiento con `prefers-reduced-motion`). Las dos capas comparten celda de grilla (sin saltos de tamaño) y solo cambian de opacidad, así que los lectores de pantalla leen ambas. Las tarjetas del hero no son enlaces: llevan `tabindex="0"` solo si tienen descripción. Las de Acompañamiento **mantienen su inclinación** en hover (solo se elevan 4 px); inclinación desktop `5deg` / `-1deg` / `-5deg` (**ojo con el signo:** Figma mide la rotación antihoraria y CSS horaria, así que "-5°" en Figma = `5deg` en CSS). En `(hover: none)` título y descripción se ven juntos en el color normal; Acompañamiento pasa a una columna rectangular por debajo de 1024.
- **Slider** (`template-parts/components/slider.php` + `slider.css` + `slider.js`): CSS scroll-snap con arrastre táctil nativo, sin librerías y **sin autoplay**. Recibe `slides` (HTML ya escapado de cada diapositiva), `label`, `item_label` ("Ir a la {foto} 2 de 3"), `arrows` (bool) y `class` (`oikos-slider--portrait` = fotos con `aspect-ratio`). Indicadores (Figma) = botones con `aria-current`, centrados sobre la foto a 16 px del pie: píldoras de 8 px de alto, `--radius-xs`, borde `--border-width-thick` del mismo color; activa 64 px `--color-decorative-caramel`, inactivas 32 px crema, 12 px entre ellas; cambian de ancho y color en `--transition-indicator` (250 ms; sin animación con movimiento reducido). Área táctil de 44 px de alto. Degradado oscuro muy sutil (25 %) al pie de las fotos. Teclado: ← → Inicio Fin en el carrusel y en los indicadores. Activa = la diapositiva con el borde inicial más cerca del desplazamiento (al final, la última); se recalcula con `IntersectionObserver` y `scrollend`, sin listener de scroll. El JS marca `.is-ready` en el contenedor y `.is-active` en la diapositiva activa, y despacha `oikos-slider:change` (`detail.index`). Modificadores: `--cards` (varias visibles: `--slider-slide-size`, `--slider-gap`, `--slider-end-space` = espacio final cuando la sección lo hace sangrar), `--dots-below` (indicadores debajo, en el flujo, centrados bajo la zona visible) y `--on-light` (fondo claro, Figma: activa interactive/secondary, inactivas `--color-brand-sand-strong`). Con 1 diapositiva no hay indicadores ni rol de carrusel. Variables (`--slider-ratio`, `--slider-radius`, `--slider-dot-*`) **sin declarar en el componente** (respaldo en `var()`): la sección que lo usa las define sin pelear especificidad. Cada plantilla que lo use se agrega a la condición de `oikos-slider` en `oikos_template_assets()`. Imágenes: la primera `eager`, el resto `lazy`.
- **Sección "Nosotros" del Inicio** (`template-parts/sections/about.php` + `about.css`): foto de fondo lazy con velo, título crema, hasta 4 tarjetas de vidrio y botón `--outline-light`; slider de fotos a la derecha (debajo en < 1024). Proporción del slider: 443/673 desktop (columna de 443 px, máx. 42 %), 5/6 tablet, 4/5 móvil.
- **Badges** (3 tarjetas, contenido global).
- **Carrusel de servicios** (`template-parts/sections/services-carousel.php` + `services-carousel.css`; Servicios general, después Nosotros y Contacto): título Heading + Body text/secondary a la izquierda (345 px, gap spacing/8) y slider `--cards --dots-below --on-light` a la derecha que **sangra hasta el borde de la pantalla** (la sección es `container-type: inline-size`, `--services-bleed` con `100cqw`, `overflow-x: clip`). < 1024 una columna (~1.5 tarjetas); < 768 ~1.15. Args `title`, `text`, `heading_level`, `cards` (null → `oikos_get_service_cards()`), `button_text`, `track_location` (`carrusel-servicios`). Al usarlo en otra plantilla, agregarla a las condiciones de `oikos-services-carousel` y `oikos-slider`.
  - **Fuente única** (`inc/services.php`): `oikos_get_service_cards()` (orden de la relación de Servicios → hijas), `oikos_services_page_id()`, `oikos_service_card_button_text()`, `oikos_is_service_page()`. Grupo ACF **"Tarjeta de servicio"** (`group_oikos_servicio_tarjeta`, lateral) en cada página de terapia: `oikos_servicio_tarjeta_imagen` (vacía → imagen destacada), `_foco_x`/`_foco_y` (0–100) y `_descripcion`. Su ubicación es la regla propia **"Página de servicio (Oikos)"** (`inc/class-oikos-acf-location-service-page.php`): hijas de la página con plantilla Servicios general (sin IDs; cuando exista `templates/servicio.php` también cuenta).
  - **Tarjeta** (`template-parts/components/therapy-card.php`, clase `oikos-therapy-card` porque `oikos-service-card` es la del Inicio): 595 × 349 en desktop (`aspect-ratio` como alto mínimo; por container query 1/1 si la tarjeta mide < 500 y 3/5 si < 340, para que la foto se siga viendo con la descripción completa). Foto `alt=""` acercada (`--card-zoom` 1.3, `transform-origin` = punto focal), título Figtree 500 (`--text-card-hero-size`) que **enlaza al servicio** (`data-track="servicio-card"`), descripción Body y botón `--light` a ancho completo al CTA global (`data-track="agenda-cita"`, `data-track-location="carrusel-servicios-<slug>"`, nombre accesible "Pide tu cita: <servicio>"). Sin enlaces anidados. Degradado text/secondary anclado al bloque de texto (llega a 0.8 donde empieza el título: AA sobre cualquier foto).
  - **Estados** con `--card-open` (0/1): reposo = foto acercada, título + 1 línea (vista previa en `::before { content: attr(data-preview) / "" }`, sin duplicar el texto en el DOM); expandida = foto a scale 1, título sube spacing/3, descripción completa con fundido. Solo scale/translate/opacity; la descripción completa siempre ocupa su lugar (sin saltos). Expandida con `:hover` (solo `hover: hover`), `:focus-within` y, en `hover: none`, la diapositiva `.is-active` (sin JS, todas expandidas). Movimiento reducido: sin zoom ni desplazamiento.
- **Tarjetas de modalidad** (Virtual / Presencial; Nosotros y sidebar del blog).
- **Tarjetas de equipo** (foto con overlay café y bio).
- **Etiqueta de categoría** (`template-parts/components/tag.php` + `tag.css`): píldora `.oikos-tag--crema` (surface/base + borde border/default, *deducido*) · `--arena` (sand, texto text/primary) · `--sage` (**`--color-brand-sage-strong` con texto crema**: el sage puro del Figma con texto claro no cumple AA). Es texto, no enlace.
- **Tarjeta de entrada** (`template-parts/components/post-card.php` + `post-card.css`): args `post_id`, `variant` (`featured` imagen 624/283 arriba, 3/2 en móvil · `horizontal` foto cuadrada 112 → 200 a la izquierda), `heading_level`, `track_location`, `image_loading`, `sizes`. **Un solo enlace** (el título, `data-track="blog-card"`) cuyo `::after` cubre la tarjeta; el foco dibuja el anillo alrededor de toda la tarjeta. Imagen `alt=""` (decorativa: el enlace ya nombra el destino). Sin imagen destacada → superficie crema del mismo tamaño. Autores "Paula · Juan David" con el punto `aria-hidden`, solo si el interruptor global de autores está encendido (arg `show_authors`: `null` = global, `true`/`false` lo fuerza).
- **Bloque de blog** "Ideas para relaciones…" (`template-parts/sections/posts-block.php` + `posts-block.css`): 1 destacada + 2 horizontales (columnas 624/454 del Figma desde 1024). Args `title`, `heading_level`, `posts` (manuales), `category` (ID o slug a priorizar: páginas de servicio), `exclude`, `link` (vacío → página de entradas con "Explora el blog"), `track_location`. Selección (`oikos_get_block_posts()`): manuales → categoría → recientes, sin repetir. Con 1 o 2 entradas se adapta (`--count-N`); con 0 no se imprime. **Tablet y móvil:** una columna, la grande arriba y las horizontales apiladas (en 2 columnas el título quedaría en ~130 px junto a la foto). En el Inicio: pestaña "Blog" (`oikos_home_blog_titulo`, `oikos_home_blog_entradas` máx. 3 y se completa con recientes, `oikos_home_blog_boton`).
- **Sección Preguntas frecuentes** (`template-parts/sections/faq.php` + `faq.css` + `faq.js`; datos y helpers en `inc/faq.php`): en cada plantilla, una línea: `oikos_the_faq_section( 'faq-<pagina>' );` (lee las preguntas de la página actual; en el Blog, de la página de entradas y solo en la página 1). Título Heading (`.oikos-faq__heading`, sin el `text-wrap: balance` global en desktop para cortar como el Figma) + botón (`data-track="agenda-cita"`) a la izquierda y preguntas a la derecha en proporción 295/751 (Figma) con gap 64; < 1024 una columna; < 768 botón a ancho completo. Debajo, el bloque WhatsApp a 32 px. CSS/JS cargan con la condición `oikos_page_has_faq()`.
  - **Preguntas con `<details>`/`<summary>` nativo** (decisión SEO: la respuesta está en el HTML, funciona sin JS y "Buscar en la página" la abre). Estructura fija: `.oikos-faq__list > details.oikos-faq__item > summary.oikos-faq__question > h3.oikos-faq__title + span.oikos-faq__chevron[aria-hidden]` y `div.oikos-faq__answer` con `<p>`/listas. Todos cerrados al cargar, sin `name` (varios abiertos a la vez). Marcador nativo oculto; el `h3` lleva el estilo de pregunta (Figtree **600** 16/140 %, medido en el Figma), no el del h3 global.
  - Ítem: surface/base, borde 1 px border/default como `box-shadow: inset` (cerrado mide exactamente 48 = `--field-height` = área táctil), `--radius-sm`, 16 entre ítems, padding horizontal 16 (medido); chevron de 20 px alineado con la primera línea; foco visible por dentro del `summary` (no lo recorta el `overflow` al animar); respuesta Body text/secondary.
  - Animación: `faq.js` anima el alto del `details` con la Web Animations API en `--transition-accordion` (250 ms), invierte sin saltos si se hace clic a mitad y pone `.is-closing` mientras cierra (el chevron vuelve a tiempo). Sin JS o con `prefers-reduced-motion` abre y cierra nativo, sin animación. No se usa `::details-content`/`interpolate-size` (Safari no los soporta y se sumaría al JS).
  - Respuesta: campo wysiwyg básico (permite enlaces y listas); `oikos_faq_clean_answer()` la normaliza (sin `<p>` vacíos ni `<p>` envolviendo listas; texto suelto → `<p>`). La misma salida alimenta la sección y el schema.
  - Schema: `inc/schema.php` → el WebPage de Yoast pasa a `['WebPage','FAQPage']` con `mainEntity` → piezas `Question` (`<url>#faq-pregunta-N`) con su `Answer`.
- **Bloque WhatsApp** (`template-parts/components/whatsapp-cta.php` + `whatsapp-cta.css`): frase + ícono forman **un solo enlace** (pestaña nueva, `rel="noopener"`, `data-track="whatsapp"`). Figma: frase en estilo Button color interactive/primary, logo de WhatsApp de 48 px en `--color-brand-whatsapp` sin fondo, gap 24, centrado. El verde del ícono no llega a 3:1, pero como la frase nombra el enlace, el ícono es de apoyo (WCAG 1.4.11 no aplica); por eso **no hay `aria-label`** (taparía el texto visible, WCAG 2.5.3): el nombre es la frase + "(WhatsApp, se abre en una pestaña nueva)" en `.screen-reader-text`. No usar el ícono solo como enlace con ese verde. Reutilizable solo (args `text`, `track_location`, `class`); al usarlo fuera de la FAQ, agregar la vista a la condición de `oikos-whatsapp-cta` en `oikos_template_assets()`. Sin número no imprime nada.
- **Testimonios** (carrusel de tarjetas con estrellas).
- **Trivia "Mitos y verdades"** (Servicios general): `template-parts/sections/trivia.php` + `trivia.css` + `trivia.js`. Args `id`, `eyebrow`, `title`, `heading_level`, `seconds` (0 = sin reloj, máx. 60), `items` (`statement`, `answer` `verdad|mito`, `explanation`), `final` (`title`, `text`, `restart_text`; vacíos → textos por defecto), `cta` (null → CTA global), `track_location` (`trivia`). Sin título o con < 2 preguntas no se imprime. Eyebrow + H2 Heading a la izquierda (345 px, gap 64) y el mazo a la derecha (carta máx. 520 px, *deducido*); < 1024 apilado y centrado.
  - **Un solo DOM:** cada `li` del mazo tiene la cara de pregunta (contador "1 de 6", reloj `clock` + segundos, afirmación `h3` entre comillas que pone el tema, botones Verdad `--primary` / Mito `--secondary`) y la de respuesta ("Se acabó el tiempo", la respuesta correcta, explicación, "Continúa" `--light`). Contador, reloj, botones y la carta final llegan con `hidden`: **sin JS** queda una lista de afirmaciones con su respuesta y explicación (contenido indexable). El JS marca `.is-ready` y arma el mazo.
  - **Mazo:** todas las cartas (y sus dos caras) comparten celda de grilla: el alto es el de la carta más alta, sin saltos. Estados `.is-active` · `.is-queued` (`--depth` 1–4: arena, corrida `--trivia-deck-step` 6 → 12 px a la derecha y −4 % de alto por carta, *deducidos*) · `.is-waiting` (invisible: más de 4 detrás o la final antes de tiempo) · `.is-gone` (sale a la izquierda con fundido). Las cartas visibles detrás = preguntas restantes (máx. 4); la final no cuenta. Al avanzar, el cambio de `--depth` hace que la siguiente "suba".
  - **Pregunta → respuesta:** fundido con cambio de color (no flip en Y: el giro muestra el texto en espejo, la perspectiva deforma la carta en 375 px y el foco quedaría dentro de algo que rota). Verde si acertó, rojo si falló o se acabó el tiempo; final azul con "Agenda tu cita" (`data-track="agenda-cita"`, `data-track-location="trivia"`) y "Repite la trivia" (vuelve a la 1 sin animar las preguntas; la final sale). Botones apilados a ancho completo < 768 en la final; Verdad/Mito en fila (apilados solo si la carta mide < 16rem).
  - **Visibilidad:** aparecer es inmediato y ocultar espera al fundido (`--trivia-hide-delay` = `--duration-deck` en los estados ocultos, que solo cambian esa variable). Así el JS mueve el foco en el mismo instante y la regla de movimiento reducido (`transition: none`) gana sin pelear especificidad.
  - **Temporizador** (`trivia.js`): deadline + `setInterval` (sin animaciones CSS: probable en headless). Arranca cuando el mazo entra en pantalla por primera vez (`IntersectionObserver`, umbral 0.5) y se pausa con la pestaña oculta (`visibilitychange`) o el mazo fuera de pantalla (decisión propia: no perder preguntas por hacer scroll). Llegar a 0 = respuesta incorrecta con "Se acabó el tiempo". Últimos 5 s: número en `--color-trivia-incorrect` y peso 700, sin parpadeo.
  - **Accesibilidad:** foco a la respuesta correcta al responder, a la afirmación siguiente al continuar y al título de la final al terminar (títulos con `tabindex="-1"`, sin anillo). Región `aria-live="polite"`: "Correcto/Incorrecto/Se acabó el tiempo: es un mito." y un único "Quedan 5 segundos." (el conteo es `aria-hidden`; el reloj lleva un texto fijo para lectores). Cartas no activas con `inert`.
  - Campos (pestaña **Trivia** del grupo "Servicios general"): `oikos_servicios_trivia_eyebrow`, `_titulo` (vacío → sin sección), `_segundos` (0–60, 20; se lee con `get_field()` porque `oikos_field()` trata 0 como vacío), `_preguntas` (repetidor 2–12: `afirmacion` sin comillas, `respuesta` Verdad/Mito, `explicacion` ≤ 220 caracteres), `_final_titulo`, `_final_texto`, `_final_repetir`. El botón de agendar es el CTA global.
- **Formularios CF7:** contacto (nombre, correo, WhatsApp, tema de consulta, mensaje), lead de El Método (nombre, correo, WhatsApp) y newsletter. Base común en `inc/forms.php` + `assets/css/forms.css` (global):
  - El tema **desactiva el CSS de CF7** y el **autop** del formulario (no el del correo): el markup lo define la plantilla de cada formulario con `.oikos-form__row` (campo + botón en fila, apilados < 768), `.oikos-form__field` (`<label>` con el texto en `.screen-reader-text` si el diseño no muestra etiqueta) y `.oikos-form__consent`.
  - El `<form>` recibe `.oikos-form` por el atributo `html_class` del shortcode; sobre fondos oscuros, además `.oikos-form--inverse` (mensajes crema).
  - El submit recibe solo `oikos-btn oikos-btn--primary` (filtro `wpcf7_form_elements`); el del formulario del newsletter, `--light` + `data-track="suscripcion"` / `data-track-location="footer-newsletter"`.
  - Estados: campo inválido con borde de 2 px `--color-feedback-error`, tip y caja de respuesta con ícono (no dependen solo del color), spinner de 16 px dentro del botón mientras envía.
  - En WAMP el correo falla (`failed`, sin `wpcf7mailsent`); para probar el `dataLayer` en local, `skip_mail: on` temporal en la configuración adicional.

### Blog
- Archivo: hero, buscador con filtro por categoría, grilla de 3 columnas, botón "Carga más" (AJAX vía REST/admin-ajax, con fallback de paginación real para SEO), FAQ.
- Entrada: breadcrumb, título, categoría, autores, extracto, banda CTA verde, contenido + sidebar sticky (modalidades + auto-pauta), relacionados "Contenido que te puede ayudar".

## SEO (obligatorio en cada plantilla)
- Un solo `<h1>` por página; jerarquía de encabezados sin saltos. HTML semántico (`header`, `main`, `nav`, `article`, `section` con encabezado, `footer`).
- Schema JSON-LD generado por el tema:
  - Sitio: `Psychologist`/`MedicalBusiness` + `LocalBusiness` con dirección en Medellín, teléfono, horario, redes.
  - Profesionales: `Person` (equipo) vinculados como autores.
  - Blog: `BlogPosting` con autor(es), `BreadcrumbList`.
  - FAQ: `FAQPage` en toda página con preguntas.
  - Servicios: `Service`.
  - **No imprimir JSON-LD suelto que duplique a Yoast.** Yoast ya genera `WebSite`, `WebPage`, `Organization`, `Article`/`BlogPosting`, `BreadcrumbList` y `Person` para autores. El tema agrega o ajusta piezas (`Psychologist`/`LocalBusiness`, `FAQPage`, `Service`, coautores del CPT `equipo`) usando los filtros de la API de schema de Yoast, para que todo quede en un solo grafo conectado.
  - Autores del equipo (`inc/schema.php`): con `oikos_entrada_autores` lleno, `Article.author` y `WebPage.author` apuntan a un `Person` por miembro (`@id` `/#/schema/person/equipo-{ID}`, con `jobTitle`, `description`, `image` y `worksFor` si el sitio es organización) y Yoast deja de generar el `Person` del usuario de WP. Filtro `oikos_schema_team_person` para agregar `sameAs`.
  - Breadcrumbs: usar `yoast_breadcrumb()` con el markup del diseño.
- Imágenes: `alt` desde la biblioteca de medios, tamaños registrados + `srcset`, WebP, `loading="lazy"` excepto la imagen LCP (hero), que lleva `fetchpriority="high"`.
- Mapa de Contacto: no cargar Google Maps en el render inicial (fachada/imagen estática que carga el iframe al interactuar).
- Enlazado interno: servicios ↔ entradas del blog de su categoría.

## Accesibilidad
**Excepción aceptada (2026-11-15, por fidelidad a Figma):** el título oscuro de las tarjetas de vidrio de "Nosotros" (Inicio) da 2.6–3.4:1 y no cumple AA. No extender esta excepción a otros componentes.

Contraste AA, foco visible, navegación por teclado (menú móvil, acordeones, carruseles, trivia, agendador), `prefers-reduced-motion` respetado.

## Analítica (GTM)
- Contenedor: **GTM-P6B7XQZW**. El ID se guarda en "Oikos — Global"; el tema imprime el snippet oficial (en `<head>` y `wp_body_open`) solo si el campo tiene valor.
- **Los eventos se configuran en GTM**, no en el tema. El tema solo da puntos de enganche estables:
  - Atributos `data-track="<accion>"` y `data-track-location="<seccion>"` en todo CTA relevante (Agenda tu cita, WhatsApp, Pide tu cita, teléfono, correo, suscripción, enlaces a servicios). Los nombres no cambian aunque cambie el diseño.
  - **Excepciones que sí empujan al `dataLayer`** desde el tema, porque GTM no las detecta solo: envío exitoso de CF7 (`wpcf7mailsent`, con id y nombre del formulario), los pasos del agendamiento y la **trivia** (`trivia.js`, siempre que esté en la página, con o sin GTM):
    - `trivia_start` (el mazo entra en pantalla por primera vez o primera respuesta), `trivia_answer` (`question_number`, `question_text`, `answer_given` `verdad|mito|''`, `is_correct`, `timed_out`), `trivia_complete` (`correct_total`, `questions_total`) y `trivia_restart`. Todos llevan `trivia_location`.
- En GTM, las etiquetas de GA4 se disparan solo con el hostname de producción; en pre-producción se valida con el modo Vista previa.

## Forma de trabajo con Claude
- **Responde siempre en español.**
- **Primero plan, después código.** Para cada componente o página, proponer el plan (archivos, campos ACF, markup) y esperar aprobación.
- **Un componente o sección por tarea.** Base del tema → tokens y tipografía → header/footer → componentes → páginas.
- Al crear campos ACF, crearlos de forma que queden en `acf-json` y nombrarlos `oikos_<seccion>_<campo>`.
- **Verificar en el navegador** cada sección contra el diseño en los 4 anchos antes de darla por terminada.
- Commits pequeños y descriptivos en español, en la rama `feature/` correspondiente.
- Después de cada merge de una rama `feature/` a `develop`, borrar esa rama local (`git branch -d`) sin preguntar.
- No instalar plugins ni dependencias nuevas sin preguntar.

## Pendientes
- [ ] **Agendamiento:** lógica completa por definir (disponibilidad, profesional, modalidad, conexión con Google Calendar, confirmación, pagos). Diseño: calendario mensual con días disponibles, duración 1 hora, selector de zona horaria, lista de horas por día.
- [ ] Plataforma del newsletter (Mailchimp, Brevo…): hoy CF7 + CFDB7. Texto de éxito del formulario 75 sigue siendo el genérico de CF7 ("Gracias por tu mensaje…").
- [ ] Página "Política de tratamiento de datos" (`/politica-de-tratamiento-de-datos/`) con el texto legal real en cada entorno (en local hay una de relleno).
- [ ] Confirmar roles exactos de los colores y colores secundarios faltantes.
- [ ] Confirmar contra Figma los valores deducidos de capturas: `radius/md` (12), estilos Card / Eyebrow / Nav y el fondo de página blanco como token.
- [ ] Testimonios del home: serán las reseñas de Google del negocio; se implementan después del paso a producción (definir si vía API de Google Places o plugin).
- [ ] Respuestas reales de las preguntas frecuentes del Inicio (hoy "[Respuesta pendiente]").
- [ ] ¿Atención presencial también en Bogotá o solo Medellín? (afecta SEO local y schema).
- [ ] ¿Páginas individuales por profesional? (recomendado para E-E-A-T en temas de salud).
- [ ] Acceso a Figma vía MCP (asiento Dev) o seguir con capturas.

## Checklist salida a producción
- [ ] Desmarcar "Disuadir a los motores de búsqueda" (Ajustes → Lectura).
- [ ] Quitar la contraseña del sitio.
- [ ] Revisar en Yoast que ninguna página importante esté en noindex.
- [ ] Enviar el sitemap a Search Console.
- [ ] Confirmar en GTM que la condición de hostname apunta al dominio final y publicar.
