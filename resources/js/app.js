import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('pwaShell', () => ({
    installEvent: null,
    online: navigator.onLine,
    installable: false,
    init() {
        window.addEventListener('online', () => this.online = true);
        window.addEventListener('offline', () => this.online = false);
        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            this.installEvent = event;
            this.installable = true;
        });
        window.addEventListener('appinstalled', () => {
            this.installable = false;
            this.installEvent = null;
        });
    },
    async installApp() {
        if (!this.installEvent) return;
        await this.installEvent.prompt();
        await this.installEvent.userChoice;
        this.installEvent = null;
        this.installable = false;
    },
}));

Alpine.start();

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => navigator.serviceWorker.register('/service-worker.js'));
}
