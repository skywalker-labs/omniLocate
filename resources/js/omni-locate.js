class OmniLocate {
    constructor(options = {}) {
        this.endpoint = options.endpoint || '/omni-locate/verify';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    /**
     * Request GPS permission and verify location with server.
     * 
     * @param {Function} onSuccess - Callback with result
     * @param {Function} onError - Callback with error
     */
    verify(onSuccess, onError) {
        if (!navigator.geolocation) {
            if (onError) onError(new Error("Geolocation not supported"));
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                this._sendToServer(position.coords.latitude, position.coords.longitude, onSuccess, onError);
            },
            (error) => {
                if (onError) onError(error);
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    }

    _sendToServer(lat, lon, onSuccess, onError) {
        fetch(this.endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lon
            })
        })
            .then(response => response.json())
            .then(data => {
                if (onSuccess) onSuccess(data);
            })
            .catch(error => {
                if (onError) onError(error);
            });
    }
}

// Attach to window
window.OmniLocate = OmniLocate;
