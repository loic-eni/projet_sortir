/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  daisyui: {
    themes: ["light", "dark", "cupcake", "coffee", "valentine", "cyberpunk", "aqua"],
  },
  theme: {
    extend: {},
  },
  plugins: [
    // require('daisyui'),
  ],
}
