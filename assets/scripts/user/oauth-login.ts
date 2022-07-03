import 'Styles/user/oauth-login.scss';
import M from 'materialize-css';
import 'materialize-css/dist/css/materialize.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import axios from 'axios';

let pipe: MessagePort;
let referrer: string;
let submittingForm = false;

window.addEventListener('message', processWindowMessage, false);
window.addEventListener('beforeunload', () => {
  if (!submittingForm) {
    pipe.postMessage(JSON.stringify({ state: 'closing' }))
  };
});

window.addEventListener('DOMContentLoaded', () => {
  referrer = (document.getElementById('referrer') as HTMLInputElement).value;
  M.AutoInit();

  // const btnCancel = document.getElementById('cancel') as HTMLButtonElement;
  // btnCancel?.addEventListener('click', () => {
  //   submittingForm = true;
  //   pipe?.postMessage(JSON.stringify({ action: 'close' }));
  // });

  document.getElementById('form-login')?.addEventListener('submit', (e) => {
    submittingForm = true;
    e.preventDefault();
    verifyCreds().then((response) => {
      sendTokens(response);
      pipe?.postMessage(JSON.stringify({ action: 'close' }));
    });
  });
});

function processWindowMessage(event: MessageEvent) {
  if (new URL(event.origin).origin !== new URL(referrer).origin || !event.isTrusted) return;

  const data = JSON.parse(event.data);
  const state = data?.state ?? null;

  switch (state) {
    case 'pipe-ready':
      pipe = event.ports[0];
      pipe.onmessage = processPipeMessage;
      pipe.postMessage(JSON.stringify({ state: 'ready' }));
      break;
    default:
  }
}

function processPipeMessage(event: MessageEvent) {
  if (event.origin !== referrer) return;

  const data = JSON.parse(event.data);
  const load = data?.load ?? null;
  const action = data?.action ?? null;
  const state = data?.state ?? null;

  switch (load) {
    default:
  }

  switch (action) {
    default:
  }

  switch (state) {
    default:
  }
}

function sendTokens(response: any) {
  pipe.postMessage(JSON.stringify({ load: { tokens: response } }));
}

function verifyCreds() {
  return new Promise((resolve, reject) => {
    const email = (document.getElementById('email') as HTMLInputElement).value;
    const password = (document.getElementById('password') as HTMLInputElement).value;
    const externalHost = (new window.URLSearchParams()).get('callsite') ?? '';
    const _csrf_token = (document.getElementById('_csrf_token') as HTMLInputElement).value;

    const formData = new FormData();
    formData.set('email', email);
    formData.set('password', password);
    formData.set('externalHost', externalHost);
    formData.set('_csrf_token', _csrf_token);

    axios({
      url: '',
      method: 'post',
      data: formData,
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      withCredentials: true,
    })
      .then((response) => resolve(response.data))
      .catch((error) => {
        console.debug(error);
        reject();
      });
  });
}
