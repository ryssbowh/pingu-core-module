class Helpers {

    ajax(url, data, type = 'POST')
    {
        let _this = this;
        $('body').css('cursor', 'wait');
        $('body').trigger('ajax.sending', data);
        return $.ajax(
            {
                type: type,
                url: url,
                data: data,
                dataType: 'json' 
            }
        ).fail(
            function (data) {
                $('body').css('cursor', 'initial');
                if(data.status == 200) { return; }
                let message = "Ajax call failed : \nStatus: " + data.status;
                if(data.responseJSON.message) {
                    message += "\nMessage : " + data.responseJSON.message;
                }
                if(data.responseJSON.exception) {
                    message += "\nException : " + data.responseJSON.exception;
                }
                Logger.logError(message);
                $('body').trigger('ajax.failure', data);
            }
        ).done(
            function (data) {
                $('body').trigger('ajax.success', data);
                $('body').css('cursor', 'initial');
            }
        );
    }

    put(url, data = {})
    {
        data._method = 'PUT';
        return this.ajax(url, data, 'POST');
    }

    _delete(url, data = {})
    {
        data._method = 'DELETE';
        return this.ajax(url, data, 'POST');
    }

    patch(url, data = {})
    {
        data._method = 'PATCH';
        return this.ajax(url, data, 'POST');
    }

    post(url, data = {})
    {
        return this.ajax(url, data, 'POST');
    }

    get(url, data = {})
    {
        return this.ajax(url, data, 'GET');
    }

    replaceUriSlugs(uri, replacements)
    {
        if(!Array.isArray(replacements)) {
            replacements = [replacements];
        }
        let match = uri.match(/(?:\G(?!^)|)(\{[\w\-]+\})/g);
        $.each(
            replacements, function (i, replacement) {
                uri = uri.replace(match[i], replacement);
            }
        );
        return uri;
    }
}

export default Helpers;