import superagentPromise from 'superagent-promise';
import _superagent from 'superagent';

const superagent = superagentPromise(_superagent, global.Promise);
const API_ROOT = 'http://bg.local/api';
const responseBody = response => {
    return response.body;
}

export const requests = {
    get: (url) => {
        return superagent.get(`${API_ROOT}${url}`)
            .then(responseBody);
    }
}