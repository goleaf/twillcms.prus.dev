import axios, { AxiosInstance } from 'axios';
import type { 
  Post, 
  PostSummary, 
  Category, 
  SiteConfig, 
  PaginatedResponse, 
  SearchResult,
  ArchiveData,
  NavigationItem,
  SearchFilters 
} from '@/types';

class ApiClient {
  private client: AxiosInstance;

  constructor() {
    this.client = axios.create({
      baseURL: '/api/v1',
      timeout: 10000,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    });
  }

  // Site endpoints
  async getSiteConfig(): Promise<SiteConfig> {
    const response = await this.client.get('/site/config');
    return response.data;
  }



  async getArchives(): Promise<ArchiveData[]> {
    const response = await this.client.get('/site/archives');
    return response.data;
  }

  // Posts endpoints
  async getPosts(params?: SearchFilters): Promise<PaginatedResponse<PostSummary>> {
    const response = await this.client.get('/posts', { params });
    return response.data;
  }

  async getPost(slug: string): Promise<Post> {
    const response = await this.client.get(`/posts/${slug}`);
    return response.data;
  }

  async getPopularPosts(limit: number = 5): Promise<PostSummary[]> {
    const response = await this.client.get('/posts/popular', { params: { limit } });
    return response.data;
  }

  async getRecentPosts(limit: number = 5): Promise<PostSummary[]> {
    const response = await this.client.get('/posts/recent', { params: { limit } });
    return response.data;
  }

  async searchPosts(query: string, filters?: Omit<SearchFilters, 'query'>): Promise<SearchResult> {
    const response = await this.client.get('/posts/search', { params: { q: query, ...filters } });
    return response.data;
  }

  async getPostsByArchive(year: number, month?: number, params?: SearchFilters): Promise<PaginatedResponse<PostSummary>> {
    const url = month ? `/posts/archive/${year}/${month}` : `/posts/archive/${year}`;
    const response = await this.client.get(url, { params });
    return response.data;
  }

  // Categories endpoints
  async getCategories(): Promise<Category[]> {
    const response = await this.client.get('/categories');
    return response.data;
  }

  async getCategory(slug: string, params?: SearchFilters): Promise<Category> {
    const response = await this.client.get(`/categories/${slug}`, { params });
    return response.data;
  }

  async getCategoryNavigation(): Promise<NavigationItem[]> {
    const response = await this.client.get('/categories/navigation');
    return response.data;
  }

  async getPopularCategories(limit: number = 5): Promise<NavigationItem[]> {
    const response = await this.client.get('/categories/popular', { params: { limit } });
    return response.data;
  }

  // Health check
  async healthCheck(): Promise<{ status: string; timestamp: string }> {
    const response = await this.client.get('/health');
    return response.data;
  }
}

// Create and export singleton instance
export const apiClient = new ApiClient();
export default apiClient; 