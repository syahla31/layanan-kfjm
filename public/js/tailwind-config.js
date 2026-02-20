tailwind.config = {
    darkMode: 'class', // Mengaktifkan mode gelap manual dengan class
    theme: {
        extend: {
            colors: {
                laravel: '#F05340', 
                primary: '#1e40af', 
                secondary: '#fbbf24',
                dark: {
                    bg: '#0f172a',
                    card: '#1e293b',
                    text: '#f1f5f9'
                }
            },
            animation: {
                'fade-in-up': 'fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                'bounce-slow': 'bounce 3s infinite',
                'float': 'float 6s ease-in-out infinite',
                'pulse-glow': 'pulseGlow 2s infinite',
            },
            keyframes: {
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
                pulseGlow: {
                    '0%, 100%': { boxShadow: '0 0 0 0 rgba(59, 130, 246, 0.4)' },
                    '50%': { boxShadow: '0 0 0 10px rgba(59, 130, 246, 0)' },
                }
            }
        }
    }
}