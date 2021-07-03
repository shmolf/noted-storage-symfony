import 'Styles/user/oauth-login.scss';
import M from 'materialize-css';
import 'materialize-css/dist/css/materialize.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

window.addEventListener('DOMContentLoaded', () => {
    M.AutoInit();

    const btnCancel = document.getElementById('cancel') as HTMLButtonElement;
    btnCancel?.addEventListener('click', () => location.replace(String(btnCancel?.dataset?.url)));
});
