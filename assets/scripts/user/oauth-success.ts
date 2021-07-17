import M from 'materialize-css';
import 'materialize-css/dist/css/materialize.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

window.addEventListener('message', (event: MessageEvent) => sendTokens(event.ports[0]), false);

window.addEventListener('DOMContentLoaded', () => {
  M.AutoInit();

  // See about extracting the referrer from the original request w/in the backend. And passing that to the JS.
  // In this way, can reference an explicit referrer rather than a `*`
  window.parent.postMessage('ready', '*');
});

function sendTokens(pipe: MessagePort) {
  const refreshTokenElem = document.getElementById('refresh-token') as HTMLInputElement;
  const refreshToken = refreshTokenElem.value;
  refreshTokenElem.parentElement?.removeChild(refreshTokenElem);

  const accessTokenElem = document.getElementById('access-token') as HTMLInputElement;
  const accessToken = accessTokenElem.value;
  accessTokenElem.parentElement?.removeChild(accessTokenElem);

  pipe.postMessage(JSON.stringify({ refreshToken, accessToken }));
}
