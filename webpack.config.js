/**
 * External dependencies
 */
const TerserPlugin = require('terser-webpack-plugin');
const minimist = require('minimist');
const path = require('path');

/**
 * WordPress dependencies
 */
const DependencyExtractionWebpackPlugin = require('@wordpress/dependency-extraction-webpack-plugin');

const options = minimist(process.argv.slice(2));
const isProduction = options.env && options.env === 'production';

const config = {
    entry: {
        app: './src/js/app.js',
        editor: './src/js/editor.js',
    },
    output: {
        filename: '[name].js',
        path: path.resolve(process.cwd(), 'assets/js'),
    },
    resolve: {
        alias: {
            'lodash-es': 'lodash',
        },
    },
    devtool: 'inline-source-map',
    optimization: {
        minimizer: [
            new TerserPlugin({
                cache: true,
                parallel: true,
                sourceMap: !isProduction,
                terserOptions: {
                    output: {
                        comments: /translators:/i,
                    },
                },
                extractComments: false,
            }),
        ],
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: [
                    require.resolve('thread-loader'),
                    {
                        loader: require.resolve('babel-loader'),
                    },
                ],
            },
        ],
    },
    plugins: [new DependencyExtractionWebpackPlugin({ injectPolyfill: true })],
};

module.exports = config;
