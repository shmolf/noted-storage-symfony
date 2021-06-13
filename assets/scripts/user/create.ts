import axios from 'axios';
import { AjaxErrorRepsonse } from 'Scripts/types/types';

let form: HTMLElement|null;
let errorOutput: HTMLElement|null;

window.addEventListener('DOMContentLoaded', () => {
    form = document.getElementById('account-details');
    errorOutput = document.getElementById('form-errors');

    form?.addEventListener('submit', (e) => {
        e.preventDefault();
        // document.getElementById('create-btn')?.setAttribute('disabled', 'disabled');
        saveUser();
    });
});

function saveUser() {
    if (errorOutput !== null) {
        errorOutput.innerHTML = '';
    }

    const url = decodeURI(String(form?.getAttribute('action')));
    const data = {
        'first-name': String((document.getElementById('first-name') as HTMLInputElement)?.value).trim(),
        'last-name': String((document.getElementById('last-name') as HTMLInputElement)?.value).trim(),
        email: String((document.getElementById('email') as HTMLInputElement)?.value).trim(),
        password: String((document.getElementById('password') as HTMLInputElement)?.value),
    };
    console.log({ data });

    axios.post(url, data, { headers: {'X-Requested-With': 'XMLHttpRequest' },})
        .then(() => window.location.href = '/login')
        .catch((error) => {
            console.error(error);

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
