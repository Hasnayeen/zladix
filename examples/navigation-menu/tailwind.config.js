const plugin = require("tailwindcss/plugin");
const { blackA, green, grass } = require("@radix-ui/colors");

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
  ],
  safelist: [
    'data-[state=open]:animate-scaleIn',
    'data-[state=closed]:animate-scaleOut',
    'data-[state=hidden]:opacity-0',
    'data-[state=visible]:animate-fadeIn',
    'data-[state=hidden]:animate-fadeOut',
    'group-data-[state=open]:-rotate-180',
  ],
  theme: {
    extend: {
      colors: {
        ...blackA,
        ...green,
        ...grass,
      },
    },
    keyframes: {
      enterFromRight: {
        from: { opacity: 0, transform: "translateX(200px)" },
        to: { opacity: 1, transform: "translateX(0)" },
      },
      enterFromLeft: {
        from: { opacity: 0, transform: "translateX(-200px)" },
        to: { opacity: 1, transform: "translateX(0)" },
      },
      exitToRight: {
        from: { opacity: 1, transform: "translateX(0)" },
        to: { opacity: 0, transform: "translateX(200px)" },
      },
      exitToLeft: {
        from: { opacity: 1, transform: "translateX(0)" },
        to: { opacity: 0, transform: "translateX(-200px)" },
      },
      scaleIn: {
        from: { opacity: 0, transform: "rotateX(-10deg) scale(0.9)" },
        to: { opacity: 1, transform: "rotateX(0deg) scale(1)" },
      },
      scaleOut: {
        from: { opacity: 1, transform: "rotateX(0deg) scale(1)" },
        to: { opacity: 0, transform: "rotateX(-10deg) scale(0.95)" },
      },
      fadeIn: {
        from: { opacity: 0 },
        to: { opacity: 1 },
      },
      fadeOut: {
        from: { opacity: 1 },
        to: { opacity: 0 },
      },
    },
  },
  animation: {
    scaleIn: "scaleIn 200ms ease",
    scaleOut: "scaleOut 200ms ease",
    fadeIn: "fadeIn 200ms ease",
    fadeOut: "fadeOut 200ms ease",
    enterFromLeft: "enterFromLeft 250ms ease",
    enterFromRight: "enterFromRight 250ms ease",
    exitToLeft: "exitToLeft 250ms ease",
    exitToRight: "exitToRight 250ms ease",
  },
  plugins: [
    plugin(({ matchUtilities }) => {
      matchUtilities({
        perspective: (value) => ({
          perspective: value,
        }),
      });
    }),
  ],
};
