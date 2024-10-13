import Api from './global/Api.js';
window.api = Api;
window.getLocale = () => localStorage.getItem('locale') || 'pt-BR';
