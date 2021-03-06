(function () {

    var isType = function (data, type) {
        return Object.prototype.toString.call(data) === ('[object ' + type + ']');
    };

    var parseRule = function (rule) {
        rule.forEach(function (c) {
            var type = c.type ? ('' + c.type).toLocaleLowerCase() : '',
                children = isType(rule.children, 'Array') ? rule.children : [];
            if ((type === 'cascader' || type === 'tree') && c.props) {
                if (c.props.data && isType(c.props.data, 'String') && c.props.data.indexOf('js.') === 0) {
                    c.props.data = window[c.props.data.substr(3)];
                }else if(c.props.options && isType(c.props.options, 'String') && c.props.options.indexOf('js.') === 0){
                    c.props.options = window[c.props.options.substr(3)];
                }
            }
            if (children.length) parseRule(children);
        });

        return rule;
    };

    var ajax = function(url, type, formData, callback){
        $.ajax({
            url: url,
            type: ('' + type).toLocaleUpperCase(),
            dataType: 'json',
            headers: headers,
            contentType: contentType,
            data: formData,
            success: function (res) {
                callback(1, res);
            },
            error: function () {
                callback(0, {});
            }
        });

    }

    var rule = parseRule(<?=$form->parseFormRule()?>), headers = <?=$form->parseHeaders()?>, config = <?=$form->parseFormConfig()?>, contentType = "<?=$form->getFormContentType()?>", action = "<?=$form->getAction()?>", method = "<?=$form->getMethod()?>", vm = new Vue();
    if (!vm.$Message && vm.$message) {
        Vue.prototype.$Message = vm.$message
    }
    return function (option) {
        if(!option) option = {};
        if (option.el) config.el = option.el;

        config.onSubmit = function (formData) {
            $f.submitBtnProps({loading: true, disabled: true});
            ajax(action, method, formData, function (status, res) {
                if (option.callback) return option.callback(status, res, $f);

                $f.submitBtnProps({loading: false, disabled: false});
                if (status && res.code === 200) {
                    vm.$Message.success(res.msg || '??????????????????');
                } else {
                    vm.$Message.error(res.msg || '??????????????????');
                }
            });
        };

        config.global = {
            upload: {
                props: {
                    onExceededSize: function (file) {
                        vm.$Message.error(file.name + '????????????????????????');
                    },
                    onFormatError: function () {
                        vm.$Message.error(file.name + '??????????????????');
                    },
                    onError: function (error) {
                        vm.$Message.error(file.name + '????????????,(' + error + ')');
                    },
                    onSuccess: function (res, file) {
                        if (res.code === 200) {
                            file.url = res.data.filePath;
                        } else {
                            vm.$Message.error(res.msg);
                        }
                    }
                }
            }
        };
        var $f = formCreate.create(rule, config);
        return $f;
    };
})();
