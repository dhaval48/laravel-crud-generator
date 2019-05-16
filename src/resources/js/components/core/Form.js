import Errors from './Errors';

class Form {
    /**
     * Create a new Form instance.
     *
     * @param {object} data
     */


    constructor(data) {
        this.originalData = data;

        for (let field in data) {
            this[field] = data[field];
        }

        this.errors = new Errors();
        this.lists = [];
    }


    /**
     * Fetch all relevant data for the form.
     */
    data() {
        let data = {};
        for (let property in this.originalData) {
            data[property] = this[property];
        }

        return data;
    }


    /**
     * Reset the form fields.
     */
    reset() {
        for (let field in this.originalData) {
            if (Array.isArray(this[field])) {
                this[field] = [""];

            } else {
               this[field] = ''; 
            }
        }
        this.success_message = '';
        this.error_message = '';
        this.errors.clear();
    }


    /**
     * Send a GET request to the given URL.
     * .
     * @param {string} url
     */
    get(url) {
        return this.submit('get', url);
    }

    /**
     * Send a POST request to the given URL.
     * .
     * @param {string} url
     */
    post(url) {
        return this.submit('post', url, this.data());
    }

     /**
     * Send a POST request with file to the given URL.
     * .
     * @param {string} url
     */
    postWithFile(url,formData) {
        return this.submit('post', url, formData);
    }


    /**
     * Send a PUT request to the given URL.
     * .
     * @param {string} url
     */
    put(url) {
        return this.submit('put', url);
    }


    /**
     * Send a PATCH request to the given URL.
     * .
     * @param {string} url
     */
    patch(url) {
        return this.submit('patch', url);
    }


    /**
     * Send a DELETE request to the given URL.
     * .
     * @param {string} url
     */
    delete(url) {
        return this.submit('delete', url);
    }


    /**
     * Submit the form.
     *
     * @param {string} requestType
     * @param {string} url
     */
    submit(requestType, url,formData) {
        this.butonLoaing(true);
        return new Promise((resolve, reject) => {
            axios[requestType](url, formData)
                .then(response => {               
                    this.butonLoaing(false);     
                    this.onSuccess(response.data);
                    this.successMessage(response.data.meta.message);
                    resolve(response.data);
                })
                .catch(error => {
                    this.onFail(error.response.data);
                    this.butonLoaing(false);
                    if(error.response.data.meta) {                    
                        this.errorMessage(error.response.data.meta.message);
                    } else {
                        this.errorMessage(error.response.data.message)
                    }
                    reject(error.response.data);
                });
        });
    }

    butonLoaing(state){
        var btn = $('.btn-loading-state');
        if(state) {            
            btn.button('loading');
        } else {
            btn.button('reset');
        }                
    }

    successMessage(message)
    {
        Vue.toasted.show(message, { 
            theme: "toasted-primary", 
            type : 'success',
            position:'top-right' ,
            icon :'check',
            iconPack:'fontawesome' ,
            duration : 5000
        });        
    }

    errorMessage(message)
    {
        Vue.toasted.show(message, { 
            theme: "toasted-primary", 
            type : 'error',
            position:'top-right' ,
            icon :'warning',
            iconPack:'fontawesome' ,
            duration : 5000
        });
    }

    /**
     * Handle a successful form submission.
     *
     * @param {object} data
     */
    onSuccess(data) {
        if(!this.originalData.id) {
            this.reset();
        }
    }


    /**
     * Handle a failed form submission.
     *
     * @param {object} errors
     */
    onFail(errors) {
        this.success_message = '';
        this.error_message = '';
        this.errors.record(errors);
    }
}

export default Form;