import 'Styles/user/new-app-token.scss';

window.addEventListener('DOMContentLoaded', () => {
    addClickCopy('pre');
});

function addClickCopy(query: string): void {
    document.querySelectorAll(query).forEach((elem: HTMLElement) => {
        elem.onclick = () => document.execCommand('copy');
        elem.addEventListener('copy', (event: ClipboardEvent) => {
            event.preventDefault();
            event.clipboardData?.setData('text/plain', elem?.textContent?.trim() ?? '');
        });
    });
}
