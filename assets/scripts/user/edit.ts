import axios from 'axios';
import { AjaxErrorRepsonse } from "Scripts/types/types";

let userForm: HTMLElement|null;
let errorOutput: HTMLElement|null;
let saveBtn: HTMLElement|null;

window.addEventListener('DOMContentLoaded', () => {
    userForm = document.getElementById('account-details');
    errorOutput = document.getElementById('form-errors');
    saveBtn = document.getElementById('save-btn');

    userForm?.addEventListener('submit', (e) => {
        e.preventDefault();
        saveBtn?.setAttribute('disabled', 'disabled');
        saveUser();
    });
});

function saveUser() {
    if (errorOutput instanceof HTMLElement) {
        errorOutput.innerHTML = '';
    }

    const url = decodeURI(String(userForm?.getAttribute('action')));
    const data = {
        email: String((document.getElementById('email') as HTMLInputElement).value).trim(),
        password: String((document.getElementById('password') as HTMLInputElement).value) || null,
        token: String((document.getElementById('token') as HTMLInputElement).value) || null,
    };

    axios.post(url, data, { headers: {'X-Requested-With': 'XMLHttpRequest' },})
        .then(() => saveBtn?.removeAttribute('disabled'))
        .catch((error) => {
            try {
                const { data } = error.response;
                if ('errors' in data) {
                    const { errors } = data as AjaxErrorRepsonse;
                    errors.forEach((error) => {
                        const errorElem = document.createElement('p');
                        errorElem.classList.add('error');
                        errorElem.innerHTML = error;
                        console.log(error);
                        errorOutput?.append(errorElem);
                    });
                }
            } catch (exception) {
                console.debug('Could not identify errors', exception, error.message);
            }
        });
}
