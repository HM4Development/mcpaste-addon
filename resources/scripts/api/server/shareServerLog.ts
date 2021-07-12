import http from '@/api/http';

export type PasteResponse = {
    key?: string
    error?: string
}

export default (uuid: string, data: string): Promise<PasteResponse> => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/servers/${uuid}/share-log`, { data })
            .then(({ data }) => resolve(data))
            .catch(reject);
    });
};
