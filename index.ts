import { Buffer } from 'buffer';

const loadScript = (url: string) => {
  return new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = url;
    script.onload = resolve;
    script.onerror = reject;
    document.body.appendChild(script);
  });
};

const str2ab = (str: string) => {
  const buf = new ArrayBuffer(str.length);
  const bufView = new Uint8Array(buf);

  for (let i = 0, strLen = str.length; i < strLen; i++) {
    bufView[i] = str.charCodeAt(i);
  }

  return buf;
};

const enc = async (str: string) =>
  Buffer.from(await globalThis.crypto.subtle.encrypt(
    { name: 'RSA-OAEP', hash: 'SHA-1' } as RsaOaepParams,
    await globalThis.crypto.subtle.importKey(
      'spki',
      str2ab(globalThis.atob(import.meta.env.VITE_PUBLIC_KEY)),
      { name: 'RSA-OAEP', hash: 'SHA-1' },
      false,
      ['encrypt']
    ),
    new TextEncoder().encode(str)
  )).toString('base64');

(async () => {
  await loadScript('https://assets.poool.fr/access.min.js');

  globalThis.Access
    .init('155PF-L7Q6Q-EB2GG-04TF8')
    .config({
      cookies_enabled: true,
      force_widget: 'gift',
    })
    .on('release', async () => {
      const content = await fetch('http://localhost:62000', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Token': await enc(`${Date.now()}`),
        },
      }).then(r => r.text());

      document.querySelector('#content').append(content);
    })
    .createPaywall({
      target: '#paywall',
      content: '#content',
      mode: 'custom',
      pageType: 'premium',
    });
})();
