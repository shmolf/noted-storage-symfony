import 'Styles/user/oauth-login.scss';
import M from 'materialize-css';
import 'materialize-css/dist/css/materialize.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

import axios from 'axios';
import { AjaxErrorRepsonse } from "Scripts/types/types";

let formLogin: HTMLElement|null;
let outputError: HTMLElement|null;
let btnLogin: HTMLElement|null;

window.addEventListener('DOMContentLoaded', () => {
    M.AutoInit();
    formLogin = document.getElementById('form-login');
    outputError = document.getElementById('form-errors');
    btnLogin = document.getElementById('login');

    formLogin?.addEventListener('submit', (e) => {
        e.preventDefault();
        btnLogin?.setAttribute('disabled', 'disabled');
        loginUser();
    });
});


function loginUser() {
    if (outputError instanceof HTMLElement) {
        outputError.innerHTML = '';
    }

    const url = decodeURI(String(formLogin?.getAttribute('action')));
    const data = {
        email: String((document.getElementById('email') as HTMLInputElement).value).trim(),
        password: String((document.getElementById('password') as HTMLInputElement).value) || null,
        token: String((document.getElementById('token') as HTMLInputElement).value) || null,
    };

    axios.post(url, data, { headers: {'X-Requested-With': 'XMLHttpRequest' } })
        .then(() => btnLogin?.removeAttribute('disabled'))
        .catch((error) => {
            try {
                const { data } = error.response;
                if ('errors' in data) {
                    const { errors } = data as AjaxErrorRepsonse;
                    errors.forEach((error) => {
                        const errorElem = document.createElement('p');
                        errorElem.classList.add('error');
                        errorElem.innerHTML = error;
                        outputError?.append(errorElem);
                    });
                }
            } catch (exception) { console.debug('Could not identify errors') }
        });
}
