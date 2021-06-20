import axios from 'axios';
import { AjaxErrorRepsonse } from "Scripts/types/types";

window.addEventListener('DOMContentLoaded', () => {
    const deleteBtns = document.querySelectorAll('.token-record button.delete');
    deleteBtns.forEach((delBtn: HTMLButtonElement) => {
        const tokenRow = delBtn.closest('.token-record') as HTMLElement;
        delBtn.addEventListener('click', () => {
            delBtn.disabled = true;
            deleteToken(decodeURI(String(tokenRow.dataset?.url).trim()))
                .then(() => tokenRow?.parentElement?.removeChild(tokenRow))
                .catch(() => delBtn.disabled = false);
        });
    });
});

function deleteToken(url: string): Promise<void> {
    return new Promise((resolve, reject) => {
        if (url === '') reject('Invalid URL');

        const outputError = document.getElementById('form-errors');

        axios.delete(url)
            .then((response) => resolve())
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
                } catch (exception) {
                    console.debug('Could not identify errors');
                }
            });
    });

}
