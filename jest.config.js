module.exports = {
  preset: 'ts-jest',
  testEnvironment: 'happy-dom',
  roots: ['<rootDir>/resources/js'],
  testMatch: [
    '**/__tests__/**/*.ts',
    '**/?(*.)+(spec|test).ts'
  ],
  transform: {
    '^.+\\.ts$': 'ts-jest',
    '^.+\\.vue$': '@vue/vue3-jest'
  },
  moduleNameMapping: {
    '^@/(.*)$': '<rootDir>/resources/js/$1'
  },
  setupFilesAfterEnv: ['<rootDir>/tests/frontend/setup.ts'],
  collectCoverageFrom: [
    'resources/js/**/*.{ts,vue}',
    '!resources/js/**/*.d.ts',
    '!resources/js/app.ts'
  ],
  coverageDirectory: 'coverage',
  coverageReporters: ['text', 'lcov', 'html']
};
module.exports = {
  preset: 'ts-jest',
  testEnvironment: 'jsdom',
  moduleNameMapping: {
    '^@/(.*)$': '<rootDir>/resources/js/$1',
    '^~/(.*)$': '<rootDir>/resources/$1',
  },
  transform: {
    '^.+\\.vue$': '@vue/vue3-jest',
    '^.+\\.(ts|tsx)$': 'ts-jest',
    '^.+\\.(js|jsx)$': 'babel-jest',
  },
  testMatch: [
    '**/tests/**/*.test.(js|jsx|ts|tsx)',
    '**/tests/**/*.spec.(js|jsx|ts|tsx)',
  ],
  moduleFileExtensions: ['js', 'jsx', 'ts', 'tsx', 'json', 'vue'],
  collectCoverageFrom: [
    'resources/js/**/*.{js,ts,vue}',
    '!resources/js/**/*.d.ts',
    '!**/node_modules/**',
  ],
  setupFilesAfterEnv: ['<rootDir>/tests/setup.ts'],
}