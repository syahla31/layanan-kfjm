@if (session('success'))
    <div id="successPopUp" class="fixed inset-0 z-[100] flex items-center justify-center p-4 transition-all duration-500">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity duration-500" id="successBackdrop"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl p-8 max-w-sm w-full text-center animate-scale-in border border-slate-100 transition-all duration-500" id="successPanel">
            <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check-circle text-5xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 mb-2">Aksi Berhasil!</h2>
            <p class="text-slate-500 text-sm mb-8 leading-relaxed">
                {{ session('success') }}
            </p>
            <button onclick="closeSuccessPopUp()" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-emerald-200 active:scale-95">
                Mengerti
            </button>

            <div class="absolute bottom-0 left-0 h-1.5 bg-emerald-500 rounded-b-3xl transition-all duration-[3000ms] ease-linear w-full" id="timerProgress"></div>
        </div>
    </div>

    <style>
        @keyframes scale-in {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-scale-in {
            animation: scale-in 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const progress = document.getElementById('timerProgress');
            if (progress) {
                setTimeout(() => {
                    progress.style.width = '0%';
                }, 10);
            }

            setTimeout(() => {
                closeSuccessPopUp();
            }, 3000);
        });

        function closeSuccessPopUp() {
            const popup = document.getElementById('successPopUp');
            const backdrop = document.getElementById('successBackdrop');
            const panel = document.getElementById('successPanel');

            if (popup) {
                backdrop.classList.add('opacity-0');
                panel.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    popup.remove();
                }, 500);
            }
        }
    </script>
@endif
