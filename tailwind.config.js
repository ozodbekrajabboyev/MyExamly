/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './index.html',
        './src/**/*.{js,ts,jsx,tsx}',
        './resources/**/*.{php,html,js,css}',
        './app/Filament/**/*.php'
    ],
    theme: {
        extend: {
            animation: {
                'fadeIn': 'fadeIn 0.3s ease-out',
                'fall': 'fall 8s linear infinite',
                'sway': 'sway 3s ease-in-out infinite'
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(-5px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' }
                },
                fall: {
                    '0%': { transform: 'translateY(-100px) translateX(0px) rotateZ(0deg)', opacity: '0' },
                    '10%': { opacity: '1' },
                    '90%': { opacity: '1' },
                    '100%': { transform: 'translateY(100vh) translateX(100px) rotateZ(360deg)', opacity: '0' }
                },
                sway: {
                    '0%, 100%': { transform: 'translateX(0px)' },
                    '50%': { transform: 'translateX(20px)' }
                }
            }
        },
    },
    plugins: [],
};
