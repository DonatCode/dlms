<?php
// $title dikirim dari masing-masing view, mis. view('site/koleksi', ['title' => 'Koleksi Buku'])
$title = $title ?? 'Perpustakaan Online';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($title) ?> | Perpustakaan Online</title>

<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

<!--
  Konfigurasi warna/tipografi/spacing ini mengikuti design tokens dari
  stitch_design_to_web_implementation/knowledge_harbor/DESIGN.md, supaya
  tampilan situs konsisten dengan mockup yang diberikan.
-->
<script>
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                "inverse-on-surface": "#f0f1f2", "primary-fixed-dim": "#adc7ff",
                "on-primary-fixed-variant": "#004493", "on-primary": "#ffffff",
                "on-error-container": "#93000a", "surface-container-high": "#e7e8e9",
                "outline": "#717786", "on-secondary-container": "#003370",
                "on-secondary-fixed-variant": "#004491", "on-error": "#ffffff",
                "secondary-fixed": "#d7e2ff", "on-tertiary-fixed": "#141d23",
                "primary-fixed": "#d8e2ff", "tertiary": "#545d65",
                "surface-dim": "#d9dadb", "on-secondary": "#ffffff",
                "on-secondary-fixed": "#001a40", "surface-tint": "#005bc0",
                "surface-container": "#edeeef", "surface-variant": "#e1e3e4",
                "on-tertiary-container": "#fcfcff", "primary-container": "#0070ea",
                "on-tertiary-fixed-variant": "#3f484f", "inverse-surface": "#2e3132",
                "background": "#f8f9fa", "secondary": "#115cb9",
                "error-container": "#ffdad6", "surface": "#f8f9fa",
                "inverse-primary": "#adc7ff", "tertiary-fixed": "#dbe4ed",
                "on-tertiary": "#ffffff", "tertiary-container": "#6d767e",
                "on-primary-container": "#fefcff", "on-surface": "#191c1d",
                "on-primary-fixed": "#001a41", "surface-container-highest": "#e1e3e4",
                "secondary-container": "#659dfe", "error": "#ba1a1a",
                "secondary-fixed-dim": "#acc7ff", "surface-container-lowest": "#ffffff",
                "primary": "#0059bb", "surface-bright": "#f8f9fa",
                "surface-container-low": "#f3f4f5", "on-background": "#191c1d",
                "outline-variant": "#c1c6d7", "on-surface-variant": "#414754",
                "tertiary-fixed-dim": "#bfc8d0"
            },
            borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
            spacing: {
                "stack-sm": "12px", "stack-md": "24px", "margin-mobile": "16px",
                "base": "8px", "margin-desktop": "40px", "stack-lg": "48px",
                "container-max": "1200px", "gutter": "24px"
            },
            fontFamily: {
                "body-lg": ["Inter"], "body-md": ["Inter"], "label-md": ["Inter"],
                "headline-md": ["Inter"], "body-sm": ["Inter"], "headline-lg": ["Inter"],
                "label-sm": ["Inter"], "headline-lg-mobile": ["Inter"], "headline-xl": ["Inter"]
            },
            fontSize: {
                "body-lg": ["18px", {lineHeight: "1.6", fontWeight: "400"}],
                "body-md": ["16px", {lineHeight: "1.5", fontWeight: "400"}],
                "label-md": ["14px", {lineHeight: "1.2", fontWeight: "600"}],
                "headline-md": ["20px", {lineHeight: "1.4", fontWeight: "600"}],
                "body-sm": ["14px", {lineHeight: "1.5", fontWeight: "400"}],
                "headline-lg": ["32px", {lineHeight: "1.3", fontWeight: "700"}],
                "label-sm": ["12px", {lineHeight: "1.2", fontWeight: "500"}],
                "headline-lg-mobile": ["24px", {lineHeight: "1.3", fontWeight: "700"}],
                "headline-xl": ["48px", {lineHeight: "1.2", letterSpacing: "-0.02em", fontWeight: "700"}]
            }
        }
    }
}
</script>
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    body { background-color: #f8f9fa; color: #191c1d; font-family: 'Inter', sans-serif; }
    .custom-shadow { box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05); }
    .card-hover { transition: transform .2s ease, box-shadow .2s ease; }
    .card-hover:hover { transform: translateY(-4px); box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.08); }
    .book-aspect { aspect-ratio: 2 / 3; }
</style>
<link href="/assets/site/site.css" rel="stylesheet">
