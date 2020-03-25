class Logger {

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
}

export default Logger;