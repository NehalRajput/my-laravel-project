import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

let echoInstance = null;

export function initializeEcho() {
    if (echoInstance) {
        return echoInstance;
    }

    try {
        const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
        const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'ap2';

        if (!pusherKey) {
            throw new Error('Pusher key is undefined');
        }

        // Disable Pusher logging in production
        Pusher.logToConsole = import.meta.env.MODE === 'development';

        echoInstance = new Echo({
            broadcaster: 'pusher',
            key: pusherKey,
            cluster: pusherCluster,
            forceTLS: true,
            encrypted: true,
        });

        // Monitor connection state changes
        echoInstance.connector.pusher.connection.bind('state_change', (states) => {
            console.group('🔄 Echo Connection State Change');
            console.log('Previous:', states.previous);
            console.log('Current:', states.current);
            console.log('Time:', new Date().toISOString());
            console.groupEnd();
        });

        // Emit custom event when connected
        echoInstance.connector.pusher.connection.bind('connected', () => {
            window.dispatchEvent(new CustomEvent('echoConnected'));
        });

        return echoInstance;
    } catch (error) {
        console.error('Error initializing Echo:', error);
        return null;
    }
}

export function getEcho() {
    return echoInstance || initializeEcho();
}

export default {
    install: (app) => {
        const echo = initializeEcho();
        app.config.globalProperties.$echo = echo;
        app.provide('echo', echo);
    }
} 