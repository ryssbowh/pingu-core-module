class Config {

    constructor()
    {
        this.config = PinguJsConfig;
    }

    get(key = false)
    {
        if(!key) {
            return this.config;
        }
        let elems = key.split('.');
        let config2 = this.config;
        elems.forEach(
            function (elem) {
                config2 = config2[elem];
            }
        );
        return config2;
    }

    setRecursive(elems, value, config)
    {
        if(elems.length == 1) {
            config[elems[0]] = value;
        }
        else{
            let elem = elems.shift();
            config[elem] = setRecursive(elems, value, config[elem]);
        }
        return config;
    }

    set(key, value)
    {
        let elems = key.split('.');
        this.config = setRecursive(elems, value, this.config);
    }

};

export default Config;