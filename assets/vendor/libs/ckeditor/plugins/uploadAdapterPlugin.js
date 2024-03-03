class UploadAdapter {
    constructor(loader) {
        this.loader = loader;
    }
    upload() {
        return this.loader.file
            .then(file => {
                const formData = new FormData();
                formData.append('image', file);
                return fetch(BASE_URL + '/ckeditor/upload', {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: 'POST',
                    body: formData
                }).then(response => {
                    if (response.status >= 200 && response.status < 300) {
                        return response.json();
                    } else {
                        throw new Error('Server returned error status: ' + response.status);
                    }
                }).then(data => {
                    if (data.uploaded) {
                        return data;
                    }
                    throw new Error('Failed to upload the file');
                });
            }).catch(error => {
                console.error(error);
                throw error;
            });
    }
    abort() {
        if (this.xhr) {
            this.xhr.abort();
        }
    }
    response() {
        return {
            default: this.default,
        };
    }
    destroy() {
        if (this.xhr) {
            this.xhr.removeEventListener('abort', this._boundAbort);
            this.xhr.removeEventListener('error', this._boundError);
            this.xhr.removeEventListener('load', this._boundLoad);
            this.xhr.removeEventListener('progress', this._boundProgress);
            this.xhr = null;
        }
    }
}