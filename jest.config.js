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
