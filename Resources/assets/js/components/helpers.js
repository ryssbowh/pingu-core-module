class Helpers {

    log(message, format = false)
    {
        if(Config.get('app.env') != 'production') {
            if (format) {
                console.log(message, format);
            } else {
                console.log(message);
            }
        }
    }

    logWarning(message)
    {
        message = '['+Config.get('app.name')+'] '+message;
        this.log("%c"+message, "color:orange");
    }

    logError(message)
    {
        message = '['+Config.get('app.name')+'] '+message;
        this.log("%c"+message, "color:red");
    }

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
                let message = "%cAjax call failed : \nStatus: " + data.status;
                if(data.responseJSON.message) {
                    message += "\nMessage : " + data.responseJSON.message;
                }
                if(data.responseJSON.exception) {
                    message += "\nException : " + data.responseJSON.exception;
                }
                _this.logError(message);
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