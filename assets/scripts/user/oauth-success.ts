import M from 'materialize-css';
import 'materialize-css/dist/css/materialize.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

let pipe: MessagePort;
let referrer: string;

window.addEventListener('message', (event: MessageEvent) => sendTokens(), false);

window.addEventListener('message', processWindowMessage, false);
window.addEventListener('beforeunload', () => pipe.postMessage(JSON.stringify({ state: 'closing' })));

window.addEventListener('DOMContentLoaded', () => {
  referrer = (document.getElementById('referrer') as HTMLInputElement).value;
  M.AutoInit();
});

function sendTokens() {
  const refreshTokenElem = document.getElementById('refresh-token') as HTMLInputElement;
  const refreshToken = refreshTokenElem.value;
  refreshTokenElem.parentElement?.removeChild(refreshTokenElem);

  const accessTokenElem = document.getElementById('access-token') as HTMLInputElement;
  const accessToken = accessTokenElem.value;
  accessTokenElem.parentElement?.removeChild(accessTokenElem);

  const pkg = {
    load: {
      tokens: {refreshToken, accessToken },
    },
  };

  pipe.postMessage(JSON.stringify(pkg));
}

function processWindowMessage(event: MessageEvent) {
  if (new URL(event.origin).origin !== new URL(referrer).origin || !event.isTrusted) return;

  const data = JSON.parse(event.data);
  const load = data?.load ?? null;
  const action = data?.action ?? null;
  const state = data?.state ?? null;

  switch (load) {
    case 'pipe':
      pipe = event.ports[0];
      pipe.onmessage = processPipeMessage;
      pipe.postMessage(JSON.stringify({ state: 'ready' }));
      break;
    default:
  }

  switch (action) {
    default:
  }

  switch (state) {
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
