import { apiClient } from '@/api/client';

// Mock fetch
const mockFetch = jest.fn();
global.fetch = mockFetch;

describe('API Client', () => {
  beforeEach(() => {
    mockFetch.mockClear();
    // Clear any cached responses
    if ('caches' in window) {
      // Mock caches API
      Object.defineProperty(window, 'caches', {
        value: {
          open: jest.fn().mockResolvedValue({
            match: jest.fn().mockResolvedValue(null),
            put: jest.fn().mockResolvedValue(undefined),
          }),
        },
      });
    }
  });

  describe('get method', () => {
    it('makes GET request with correct URL and headers', async () => {
      const mockResponse = { data: 'test' };
      mockFetch.mockResolvedValueOnce({
        ok: true,
        status: 200,
        json: jest.fn().mockResolvedValue(mockResponse),
        headers: new Headers({
          'content-type': 'application/json',
        }),
      });

      const result = await apiClient.get('/test-endpoint');

      expect(mockFetch).toHaveBeenCalledWith('/api/v1/test-endpoint', {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
      });
      expect(result).toEqual(mockResponse);
    });

    it('handles query parameters correctly', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        status: 200,
        json: jest.fn().mockResolvedValue({}),
        headers: new Headers(),
      });

      await apiClient.get('/posts', { page: 1, per_page: 10 });

      expect(mockFetch).toHaveBeenCalledWith(
        '/api/v1/posts?page=1&per_page=10',
        expect.any(Object)
      );
    });

    it('throws error for non-ok responses', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: false,
        status: 404,
        statusText: 'Not Found',
        json: jest.fn().mockResolvedValue({ message: 'Not found' }),
        headers: new Headers(),
      });

      await expect(apiClient.get('/nonexistent')).rejects.toThrow('HTTP 404: Not Found');
    });

    it('handles network errors', async () => {
      mockFetch.mockRejectedValueOnce(new Error('Network error'));

      await expect(apiClient.get('/test')).rejects.toThrow('Network error');
    });
  });

  describe('post method', () => {
    it('makes POST request with data', async () => {
      const mockResponse = { id: 1 };
      const postData = { title: 'Test Post' };

      mockFetch.mockResolvedValueOnce({
        ok: true,
        status: 201,
        json: jest.fn().mockResolvedValue(mockResponse),
        headers: new Headers(),
      });

      const result = await apiClient.post('/posts', postData);

      expect(mockFetch).toHaveBeenCalledWith('/api/v1/posts', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(postData),
      });
      expect(result).toEqual(mockResponse);
    });
  });

  describe('caching', () => {
    it('caches GET requests when cache is available', async () => {
      const mockResponse = { data: 'cached' };
      
      // Mock cache hit
      const mockCache = {
        match: jest.fn().mockResolvedValue({
          json: jest.fn().mockResolvedValue(mockResponse),
        }),
        put: jest.fn(),
      };

      Object.defineProperty(window, 'caches', {
        value: {
          open: jest.fn().mockResolvedValue(mockCache),
        },
      });

      const result = await apiClient.get('/cached-endpoint');

      expect(result).toEqual(mockResponse);
      expect(mockFetch).not.toHaveBeenCalled(); // Should use cache
    });
  });

  describe('error handling', () => {
    it('handles JSON parsing errors', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: false,
        status: 500,
        statusText: 'Internal Server Error',
        json: jest.fn().mockRejectedValue(new Error('Invalid JSON')),
        text: jest.fn().mockResolvedValue('Server Error'),
        headers: new Headers(),
      });

      await expect(apiClient.get('/error')).rejects.toThrow('HTTP 500: Internal Server Error');
    });
  });
});
