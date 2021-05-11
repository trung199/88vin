'use strict';

const JavaScriptObfuscator = require('webpack-obfuscator');

module.exports = {
    entry: {
        'main': './public/main.js'
    },
    output: {
        path: 'D:/88vin/onlineNode/build',
        filename: '[name].js' // output: abc.js, cde.js
    },
    plugins: [
        new JavaScriptObfuscator({
            rotateStringArray: true
        }, ['bundle.js'])
    ]
};