const Config = (() => {

    let config;

    function init()
    {
        console.log('Config initialized');
        config = PinguJsConfig;
    }

    function get(key)
    {
        if(!key) {
            return config;
        }
        let elems = key.split('.');
        let config2 = config;
        elems.forEach(
            function (elem) {
                config2 = config2[elem];
            }
        );
        return config2;
    }

    function setRecursive(elems, value, config)
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

    function set(key, value)
    {
        let elems = key.split('.');
        config = setRecursive(elems, value, config);
    }

    return {
        init: init,
        get: get,
        set: set
    };

})();

export default Config;