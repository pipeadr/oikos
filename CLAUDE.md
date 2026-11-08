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
### Colores (roles por confirmar contra Figma)
| Variable | Hex | Uso observado |
|---|---|---|
| `--oikos-green` | #2D5545 | Primario: botones, CTA, header |
| `--oikos-brown` | #8E5325 | Footer, botones secundarios, tarjeta "pareja" |
| `--oikos-sage` | #80957D | Gradientes verdes, tags |
| `--oikos-sand` | #D1A980 | Tarjetas beige de temas, tags |
| `--oikos-brown-dark` | #4B3A2F | Overlays de tarjetas de equipo |
| `--oikos-caramel` | #A87A53 | Acentos |
| `--oikos-text` | #212121 | Texto principal |
| `--oikos-cream` | #FBF5E9 | Fondos crema, nav, tarjetas |

### Tipografía
- **Bricolage Grotesque** → títulos. **Figtree** → textos y UI.
- Auto-hospedadas en woff2, `font-display: swap`, precargar solo los pesos usados en el primer pantallazo.
- Escala fluida con `clamp()` (definir en `base.css` a partir del diseño; no hay escala oficial en Figma).

### Estética general
Bordes redondeados generosos, tarjetas crema sobre fondos crema/blanco, gradientes verde→sage en bandas destacadas, ilustraciones de líneas orgánicas como decoración (SVG, `aria-hidden`).

## Mapa del sitio, plantillas y URLs
| Página | URL | Plantilla |
|---|---|---|
| Inicio | `/` | `front-page.php` |
| Servicios (general) | `/servicios/` | Servicios general |
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
- **CPT `equipo`** (psicólogos: Paula, Juan David): foto, nombre, enfoque, bio corta, bio larga. Se usa en servicios, nosotros, páginas de terapia y como **autores del blog** (relación ACF; una entrada puede tener varios autores).
- **Categorías del blog:** Terapia de pareja, Terapia individual, Terapia familiar (se muestran como tag de color).
- **Página de opciones ACF "Oikos — Global":** contacto (teléfono, WhatsApp, correo, dirección), redes sociales, textos y enlaces del CTA "Agenda tu cita", los 3 badges ("Psicólogos especializados…", "100% confidencial…", "Presencial en Medellín…"), textos del bloque newsletter, banner de auto-pauta del blog (300×600), "¿Tienes dudas? Escríbenos" + WhatsApp.
- **Testimonios:** repetidor en el Home (solo aparecen ahí). Si se reutilizan en otra página, migrar a CPT.
- **Preguntas frecuentes:** repetidor por página (cada página tiene sus propias preguntas). Siempre generan schema `FAQPage`.

## Componentes reutilizables (construir una vez)
- **Header** (nav flotante crema con píldoras + CTA) y **footer** (banda newsletter verde + bloque café con CTA, menús, contacto, redes). Menús administrables desde Apariencia → Menús.
- **Hero** en sus variantes (foto a pantalla completa con texto; foto + texto en dos columnas; foto en B/N con texto).
- **Grilla de tarjetas de temas** (beige, 2×3 o 2×4).
- **Pasos numerados** ("Cómo funciona"/"Así es una sesión", 3 pasos).
- **Badges** (3 tarjetas, contenido global).
- **Carrusel de servicios** (Servicios general, Nosotros, Contacto).
- **Tarjetas de modalidad** (Virtual / Presencial; Nosotros y sidebar del blog).
- **Tarjetas de equipo** (foto con overlay café y bio).
- **Bloque de blog** "Ideas para relaciones…" (1 destacado + 2 laterales). En páginas de servicio, priorizar entradas de la categoría correspondiente.
- **Acordeón de FAQ** (accesible: `button` + `aria-expanded`; contenido presente en el HTML para SEO).
- **Testimonios** (carrusel de tarjetas con estrellas).
- **Trivia "Mitos y verdades"** (Servicios general): interactiva, N preguntas, temporizador, botones Verdad/Mito, contenido por repetidor ACF. JS propio, carga solo en esa plantilla.
- **Formularios CF7:** contacto (nombre, correo, WhatsApp, tema de consulta, mensaje) y lead de El Método (nombre, correo, WhatsApp). Estilizados con las clases del tema.
- **Newsletter** *(plataforma pendiente)*.

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
  - Breadcrumbs: usar `yoast_breadcrumb()` con el markup del diseño.
- Imágenes: `alt` desde la biblioteca de medios, tamaños registrados + `srcset`, WebP, `loading="lazy"` excepto la imagen LCP (hero), que lleva `fetchpriority="high"`.
- Mapa de Contacto: no cargar Google Maps en el render inicial (fachada/imagen estática que carga el iframe al interactuar).
- Enlazado interno: servicios ↔ entradas del blog de su categoría.

## Accesibilidad
Contraste AA, foco visible, navegación por teclado (menú móvil, acordeones, carruseles, trivia, agendador), `prefers-reduced-motion` respetado.

## Analítica (GTM)
- Contenedor: **GTM-P6B7XQZW**. El ID se guarda en "Oikos — Global"; el tema imprime el snippet oficial (en `<head>` y `wp_body_open`) solo si el campo tiene valor.
- **Los eventos se configuran en GTM**, no en el tema. El tema solo da puntos de enganche estables:
  - Atributos `data-track="<accion>"` y `data-track-location="<seccion>"` en todo CTA relevante (Agenda tu cita, WhatsApp, Pide tu cita, teléfono, correo, suscripción, enlaces a servicios). Los nombres no cambian aunque cambie el diseño.
  - **Excepciones que sí empujan al `dataLayer`** desde el tema, porque GTM no las detecta solo: envío exitoso de CF7 (`wpcf7mailsent`, con id y nombre del formulario) y los pasos del agendamiento.
- En GTM, las etiquetas de GA4 se disparan solo con el hostname de producción; en pre-producción se valida con el modo Vista previa.

## Forma de trabajo con Claude
- **Primero plan, después código.** Para cada componente o página, proponer el plan (archivos, campos ACF, markup) y esperar aprobación.
- **Un componente o sección por tarea.** Base del tema → tokens y tipografía → header/footer → componentes → páginas.
- Al crear campos ACF, crearlos de forma que queden en `acf-json` y nombrarlos `oikos_<seccion>_<campo>`.
- **Verificar en el navegador** cada sección contra el diseño en los 4 anchos antes de darla por terminada.
- Commits pequeños y descriptivos en español, en la rama `feature/` correspondiente.
- No instalar plugins ni dependencias nuevas sin preguntar.

## Pendientes
- [ ] **Agendamiento:** lógica completa por definir (disponibilidad, profesional, modalidad, conexión con Google Calendar, confirmación, pagos). Diseño: calendario mensual con días disponibles, duración 1 hora, selector de zona horaria, lista de horas por día.
- [ ] Plataforma del newsletter (Mailchimp, Brevo…).
- [ ] Confirmar roles exactos de los colores y colores secundarios faltantes.
- [ ] Testimonios reales (con autorización de los pacientes).
- [ ] ¿Atención presencial también en Bogotá o solo Medellín? (afecta SEO local y schema).
- [ ] ¿Páginas individuales por profesional? (recomendado para E-E-A-T en temas de salud).
- [ ] Acceso a Figma vía MCP (asiento Dev) o seguir con capturas.

## Checklist salida a producción
- [ ] Desmarcar "Disuadir a los motores de búsqueda" (Ajustes → Lectura).
- [ ] Quitar la contraseña del sitio.
- [ ] Revisar en Yoast que ninguna página importante esté en noindex.
- [ ] Enviar el sitemap a Search Console.
- [ ] Confirmar en GTM que la condición de hostname apunta al dominio final y publicar.
