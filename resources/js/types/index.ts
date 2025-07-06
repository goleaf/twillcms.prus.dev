// Core data types
export interface Post {
  id: number;
  title: string;
  slug: string;
  description: string;
  content: string;
  published: boolean;
  published_at: string;
  created_at: string;
  updated_at: string;
  meta: PostMeta;
  categories: Category[];
  images?: MediaItem[];
  reading_time?: number;
  related_posts?: PostSummary[];
}

export interface PostSummary {
  id: number;
  title: string;
  slug: string;
  description: string;
  published_at: string;
  created_at: string;
  meta: {
    url: string;
    reading_time?: number;
  };
  categories?: CategorySummary[];
  featured_image?: MediaItem;
  excerpt?: string;
}

export interface PostMeta {
  title: string;
  description: string;
  keywords: string;
  canonical_url: string;
  og_image?: string;
}



export interface Category {
  id: number;
  name: string;
  slug: string;
  description: string;
  color: string;
  position: number;
  published: boolean;
  created_at: string;
  updated_at: string;
  meta: CategoryMeta;
  posts_count?: number;
  posts?: {
    data: PostSummary[];
    meta: PaginationMeta;
    links: PaginationLinks;
  };
  image?: MediaItem;
}

export interface CategorySummary {
  id: number;
  name: string;
  slug: string;
  color: string;
}

export interface CategoryMeta {
  title: string;
  description: string;
  canonical_url: string;
}



export interface MediaItem {
  id?: number;
  url: string;
  thumb?: string;
  alt: string;
  caption?: string;
}

// API response types
export interface ApiResponse<T> {
  data: T;
  meta?: any;
  links?: any;
}

export interface PaginatedResponse<T> {
  data: T[];
  meta: PaginationMeta;
  links: PaginationLinks;
}

export interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from?: number;
  to?: number;
}

export interface PaginationLinks {
  first: string;
  last: string;
  prev?: string;
  next?: string;
}

// Site configuration
export interface SiteConfig {
  site: {
    name: string;
    description: string;
    url: string;
    locale: string;
    timezone: string;
  };
  meta: {
    generator: string;
    version: string;
    api_version: string;
  };
  social: {
    twitter?: string;
    github?: string;
  };
  features: {
    search: boolean;
    categories: boolean;
    archives: boolean;
    rss: boolean;
  };
}

// Archive data
export interface ArchiveData {
  year: number;
  total: number;
  months: ArchiveMonth[];
  url: string;
}

export interface ArchiveMonth {
  month: number;
  name: string;
  count: number;
  url: string;
}

// Search and filters
export interface SearchFilters {
  query?: string;
  category?: string;
  year?: number;
  month?: number;
  page?: number;
  per_page?: number;
}

export interface SearchResult {
  data: PostSummary[];
  meta: PaginationMeta & {
    query?: string;
    category?: string;
  };
  links: PaginationLinks;
}

// Navigation
export interface NavigationItem {
  id: number;
  name: string;
  slug: string;
  color: string;
  posts_count: number;
  url: string;
}

// Loading and error states
export interface LoadingState {
  posts: boolean;
  post: boolean;
  categories: boolean;
  search: boolean;
  config: boolean;
}

export interface ErrorState {
  posts?: string;
  post?: string;
  categories?: string;
  search?: string;
  config?: string;
  general?: string;
}

// Router meta
export interface RouteMeta {
  title?: string;
  description?: string;
  keywords?: string;
  ogImage?: string;
  canonical?: string;
  noIndex?: boolean;
}

// Component props
export interface PostCardProps {
  post: PostSummary;
  showCategory?: boolean;
  showExcerpt?: boolean;
  showDate?: boolean;
  showReadingTime?: boolean;
}

export interface CategoryCardProps {
  category: Category | NavigationItem;
  showPostCount?: boolean;
  showDescription?: boolean;
}

export interface PaginationProps {
  meta: PaginationMeta;
  links: PaginationLinks;
  showInfo?: boolean;
}

// Form types
export interface SearchForm {
  query: string;
  category: string;
}

 