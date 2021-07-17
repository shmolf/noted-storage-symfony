import M from 'materialize-css';
import 'materialize-css/dist/css/materialize.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

let pipe: MessagePort;

window.addEventListener('message', (event: MessageEvent) => pipe = event.ports[0], false);

window.addEventListener('DOMContentLoaded', () => {
  M.AutoInit();

  const refreshToken = document.getElementById('refresh-token') as HTMLInputElement;
  const accessToken = document.getElementById('access-token') as HTMLInputElement;
  pipe.postMessage(JSON.stringify({ refreshToken, accessToken }));
});
