class Api
{
    static BASE_URL = 'http://localhost:8080/api/v1/';

    /**
     * Perform a GET fetch request.
     *
     * @param {string} url
     */
    static async get(url)
    {
        try {
            const options = {
                headers: {
                    Accept: 'application/json',
                }
            };

            const token = localStorage.getItem('token');
            if(token) {
                options.headers['Authorization'] = `Bearer ${token}`;
            }

            const response = await fetch(Api.prepareURL(url), options);

            response.data = await response.json();

            return response;
        } catch(e) {
            console.error(e);
            return null;
        }
    }

    /**
     * Perform a POST fetch request.
     *
     * @param {string} url
     * @param {object} data
     */
    static async post(url, data = {})
    {
        try {
            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            };

            const token = localStorage.getItem('token');
            if(token) {
                options.headers['Authorization'] = `Bearer ${token}`;
            }

            if(data instanceof FormData) {
                options.body = data;
                delete options.headers['Content-Type'];
            }

            const response = await fetch(Api.prepareURL(url), options);

            if(response.status !== 204) {
                response.data = await response.json();
            }

            return response;
        } catch(e) {
            console.error(e);
            return null;
        }
    }

    /**
     * Sanitizes the URL by removing all non-alphanumeric characters.
     *
     * Also removes any trailing slash.
     *
     * @param {string} url
     * @internal
     */
    static prepareURL(url)
    {
        url = url.toLowerCase()
            .replace(/\\/g, '/')
            .replace(/[^a-z0-9\/\.-:\?\=]/g, '')
            .replace(/(^\/+)|(\/+$)+/g, '');

        return !url.startsWith('http://') && !url.startsWith('https://')
            ? Api.BASE_URL.concat(url)
            : url;
    }
}

export default Api;
