const {
  spacing
} = require('tailwindcss/defaultTheme');

const colors = require('tailwindcss/colors');

const hyvaModules = require('@hyva-themes/hyva-modules');

module.exports = hyvaModules.mergeTailwindConfig({
  theme: {
    extend: {
      animation: {
        'infinite-scroll': 'infinite-scroll 25s linear infinite',
        'infinite-scroll-brands': 'infinite-scroll 120s linear infinite',
        tilt: "tilt 7s linear infinite",
        border: 'border 5s linear infinite',
      },
      keyframes: {
        'infinite-scroll': {
          from: { transform: 'translateX(0)' },
          to: { transform: 'translateX(-100%)' },
        },
        tilt: {
          "0%, 50%, 100%": { transform: "rotate(0deg)" },
          "25%": { transform: "rotate(1deg)" },
          "75%": { transform: "rotate(-1deg)" },
        },
        border: {
          'to': { '--border-angle': '360deg' },
        }
      },
      boxShadow: {
        "search": "2px 2px 8px rgba(0, 0, 0, 0.1)",
        "search-hover": "4px 4px 8px rgba(0, 0, 0, 0.2)",
        "whizzy-base":
          "0 12px 60px rgba(14,32,66,.12), 0 0px 20px rgba(14,32,66,.05)",
        "whizzy-base-hover":
          "0 12px 60px rgba(14,32,66,.15), 0 0px 20px rgba(14,32,66,.07)",
        "card-interactive": "0 0 30px rgba(0, 0, 0, 0.1)",
        "inside-negative": "inset 0 -5px 8px 0 rgb(0 0 0 / 0.10)",
        "whizzy-header": "0 8px 40px rgba(1,22,36,.08)",
        "whizzy-menu-negative": "0 8px 40px rgba(1, 22, 36,.08)",
        "whizzy-menu-negative-inv": "0 -8px 40px rgba(1, 22, 36,.08)",
        "whizzy-submenu": '0 12px 60px rgba(14,32,66,.15), 0 1px 2px rgba(14,32,66,.05)',
        "whizzy-nsubmenu": '0 -12px 60px rgba(14,32,66,.15), 0 1px 2px rgba(14,32,66,.05)',
        "whizzy-checkout-inset": 'inset -20px 0 40px -20px rgba(1, 22, 36, .08)',
      },
      backgroundImage: {
        'radial-center': 'radial-gradient(50% 50% at 50% 50%,#0000 0,#00000080 100%)',
      },
      screens: {
        'sm': '640px',
        // => @media (min-width: 640px) { ... }
        'md': '768px',
        // => @media (min-width: 768px) { ... }
        'lg': '1024px',
        // => @media (min-width: 1024px) { ... }
        'xl': '1280px',
        // => @media (min-width: 1280px) { ... }
        '2xl': '1632px' // => @media (min-width: 1536px) { ... }

      },
      fontFamily: {
        sans: ["Inter", "Helvetica Neue", "Arial", "sans-serif"]
      },
      colors: {
        whizzy: {
          'body': '#06132b',
          'artic': "#F2F4F5",
          'primary': '#0033AB',
          'primary-darker': '#01216bff',
          'primary-lighter': '#EBF0F8',
          'secondary': '#00AFEF',
          'secondary-darker': '#011624',
          'graylighter': '#67737C',
          'separator': '#E5E7EB',
        },
        primary: {
          lighter: colors.gray["700"],
          "DEFAULT": colors.black,
          darker: colors.black,
        },
        secondary: {
          lighter: colors.gray["100"],
          "DEFAULT": colors.gray["200"],
          darker: colors.gray["300"],
        },
        background: {
          lighter: colors.gray["100"],
          "DEFAULT": colors.gray["200"],
          darker: colors.gray["300"],
        },
        green: colors.emerald,
        yellow: colors.yellow,
        orange: colors.amber,
        purple: colors.violet,
        gray: colors.gray,
        black: "#000",
        white: "#fff",
        transparent: "transparent",
        current: "currentColor",
      },
      textColor: {
        orange: colors.orange,
        red: {
          ...colors.red,
          "DEFAULT": colors.red['500']
        },
        primary: {
          lighter: colors.gray["800"],
          "DEFAULT": colors.black,
          darker: colors.black,
        },
        secondary: {
          lighter: colors.gray["400"],
          "DEFAULT": colors.gray["600"],
          darker: colors.gray["800"],
        },
      },
      backgroundColor: {
        primary: {
          lighter: colors.gray['600'],
          "DEFAULT": colors.gray['700'],
          darker: colors.gray['800']
        },
        secondary: {
          lighter: colors.gray['100'],
          "DEFAULT": colors.gray['200'],
          darker: colors.gray['300']
        },
        container: {
          lighter: '#ffffff',
          "DEFAULT": '#fafafa',
          darker: '#f5f5f5'
        }
      },
      borderColor: {
        primary: {
          lighter: colors.blue['600'],
          "DEFAULT": colors.blue['700'],
          darker: colors.blue['800']
        },
        secondary: {
          lighter: colors.blue['100'],
          "DEFAULT": colors.blue['200'],
          darker: colors.blue['300']
        },
        container: {
          lighter: '#f5f5f5',
          "DEFAULT": '#e7e7e7',
          darker: '#b6b6b6'
        }
      },
      minWidth: {
        8: spacing["8"],
        20: spacing["20"],
        40: spacing["40"],
        48: spacing["48"]
      },
      minHeight: {
        14: spacing["14"],
        a11y: '44px',
        'screen-25': '25vh',
        'screen-50': '50vh',
        'screen-75': '75vh'
      },
      maxHeight: {
        '0': '0',
        'screen-25': '25vh',
        'screen-50': '50vh',
        'screen-75': '75vh'
      },
      container: {
        center: true,
        padding: '1.5rem'
      },
      padding: {
        '1/4': '25%',
        '1/3': '33.3333%',
        '1/2': '50%',
        '2/3': '66.6666%',
        '3/4': '75%',
        '11/12': '91.666667%',
        full: '100%',
      },
      zIndex: {
        'auto': 'auto',
        'n2': -2,
        'n1': -1,
        '0': 0,
        '1': 1,
        '2': 2
      }
    }
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
  // Examples for excluding patterns from purge
  content: [
    // this theme's phtml and layout XML files
    '../../**/*.phtml',
    '../../*/layout/*.xml',
    '../../*/page_layout/override/base/*.xml',
    // parent theme in Vendor (if this is a child-theme)
    '../../../../../../../vendor/hyva-themes/magento2-default-theme/**/*.phtml',
    '../../../../../../../vendor/hyva-themes/magento2-default-theme/*/layout/*.xml',
    '../../../../../../../vendor/hyva-themes/magento2-default-theme/*/page_layout/override/base/*.xml',
    // app/code phtml files (if need tailwind classes from app/code modules)
    '../../../../../../../app/code/**/*.phtml',
  ]
});
