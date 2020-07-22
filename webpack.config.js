/**
 * External dependencies
 */
const webpack = require("webpack");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const TerserPlugin = require("terser-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const path = require("path");
const glob = require("glob");

/**
 * WordPress dependencies
 */
const DependencyExtractionWebpackPlugin = require("@wordpress/dependency-extraction-webpack-plugin");
const postcssPlugins = require("@wordpress/postcss-plugins-preset");

const isProduction = process.env.NODE_ENV === "production";

const config = {
    mode: isProduction ? "production" : "development",
    entry: {
        "js/app": "./src/js/app.js",
        // Process all SCSS files except partials.
        ...glob.sync("./src/scss/**.scss").reduce((obj, el) => {
            const name = path.parse(el).name;

            if (!name.startsWith("_")) {
                obj["css/" + path.parse(el).name] = el;
            }

            return obj;
        }, {}),
    },
    output: {
        filename: "[name].js",
        path: path.resolve(process.cwd(), "assets"),
    },
    resolve: {
        alias: {
            "lodash-es": "lodash",
        },
    },
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
                    require.resolve("thread-loader"),
                    {
                        loader: require.resolve("babel-loader"),
                        options: {
                            babelrc: false,
                            configFile: false,
                            presets: [
                                require.resolve(
                                    "@wordpress/babel-preset-default"
                                ),
                            ],
                        },
                    },
                ],
            },
            {
                test: /\.scss$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                    },
                    {
                        loader: require.resolve("css-loader"),
                        options: {
                            url: false,
                            sourceMap: !isProduction,
                        },
                    },
                    {
                        loader: require.resolve("postcss-loader"),
                        options: {
                            ident: "postcss",
                            plugins: postcssPlugins,
                        },
                    },
                    {
                        loader: require.resolve("sass-loader"),
                        options: {
                            sourceMap: !isProduction,
                        },
                    },
                ],
            },
        ],
    },
    plugins: [
        new CleanWebpackPlugin({
            protectWebpackAssets: false,
            cleanAfterEveryBuildPatterns: ["css/**/*", "!css/**/*.css"],
        }),
        new MiniCssExtractPlugin({ esModule: false, filename: "[name].css" }),
        new DependencyExtractionWebpackPlugin({ injectPolyfill: true }),
        new webpack.BannerPlugin({
            banner: "WOA",
            include: /style.css/,
        }),
    ].filter(Boolean),
};

module.exports = config;
