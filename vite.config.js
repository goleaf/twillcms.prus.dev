import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
                compilerOptions: {
                    // Enable production optimizations
                    hoistStatic: true,
                    cacheHandlers: true,
                }
            },
        }),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
        },
    },
    build: {
        // Optimize chunk size limits
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                // Advanced chunk splitting for better caching
                manualChunks: (id) => {
                    // Vendor libraries
                    if (id.includes('node_modules')) {
                        if (id.includes('vue') || id.includes('pinia') || id.includes('vue-router')) {
                            return 'vue-core';
                        }
                        if (id.includes('axios')) {
                            return 'http-client';
                        }
                        if (id.includes('@vueuse') || id.includes('lodash')) {
                            return 'utilities';
                        }
                        if (id.includes('tailwindcss')) {
                            return 'styles';
                        }
                        return 'vendor';
                    }
                    // App components by feature
                    if (id.includes('components/layout')) {
                        return 'layout';
                    }
                    if (id.includes('views/') || id.includes('pages/')) {
                        return 'pages';
                    }
                    if (id.includes('stores/')) {
                        return 'stores';
                    }
                },
                // Optimize file naming for better caching
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]',
            },
        },
        sourcemap: false,
        minify: 'terser',
        terserOptions: {
            compress: {
                // Aggressive compression
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug'],
                passes: 2,
                unsafe: true,
                unsafe_comps: true,
                unsafe_math: true,
                unsafe_proto: true,
            },
            mangle: {
                safari10: true,
            },
            format: {
                comments: false,
            },
        },
        // Enable CSS code splitting
        cssCodeSplit: true,
        // Optimize asset inlining threshold
        assetsInlineLimit: 4096,
        // Enable tree shaking
        target: 'esnext',
        modulePreload: {
            polyfill: false
        },
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
    // Production optimizations
    esbuild: {
        // Remove console logs in production
        drop: ['console', 'debugger'],
        legalComments: 'none',
    },
    // Enable experimental features
    experimental: {
        renderBuiltUrl(filename, { hostType }) {
            if (hostType === 'js') {
                return { js: `/${filename}` };
            }
            return { relative: true };
        }
    }
});
