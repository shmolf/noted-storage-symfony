import 'Styles/inputs/password.scss';

window.addEventListener('DOMContentLoaded', () => {
    const toggleBtns = document.querySelectorAll('.toggle-password-visibility') as NodeListOf<HTMLElement>;

    toggleBtns.forEach((toggleBtn) => {
        toggleBtn.addEventListener('click', (e:MouseEvent) => {
            const toggleBtn = e.currentTarget as HTMLButtonElement;
            const inputSelector:string = String(toggleBtn.dataset.targetSelector).trim();

            (document.querySelectorAll(inputSelector) as NodeListOf<HTMLElement>).forEach((input) => {
                const isPasswordType = String(input.getAttribute('type') ?? '').trim() === 'password';
                input.setAttribute('type', isPasswordType ? 'text' : 'password');
                toggleBtn.classList.toggle('fa-eye', !isPasswordType);
                toggleBtn.classList.toggle('fa-eye-slash', isPasswordType);
            });
        });
    });
});
