const { merge } = require('webpack-merge');
const common = require('./webpack.common.js');

module.exports = merge(common, {
    mode: 'development',
    //watch: true,
    watchOptions: {
        ignored: ['vendor/**', 'public_html/**', 'node_modules/**', 'ws/**']
    },
    devtool: "source-map",
});
