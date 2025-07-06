<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
    <!-- Hero Section -->
    <section class="relative bg-white overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 opacity-5"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        <div class="text-center">
          <h1 class="text-5xl lg:text-7xl font-bold text-gray-900 mb-8 leading-tight">
            About Our Story
          </h1>
          <p class="text-xl lg:text-2xl text-gray-600 mb-12 max-w-4xl mx-auto leading-relaxed">
            We're passionate storytellers, dedicated to sharing insights, experiences, and knowledge 
            that inspire and inform our community. Our mission is to create meaningful content that makes a difference.
          </p>
          <div class="grid gap-8 md:grid-cols-3 max-w-4xl mx-auto">
            <div class="text-center">
              <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                <PencilIcon class="h-8 w-8 text-blue-600" />
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Quality Content</h3>
              <p class="text-gray-600">Carefully crafted articles with deep research and authentic insights</p>
            </div>
            <div class="text-center">
              <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                <UsersIcon class="h-8 w-8 text-indigo-600" />
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Community Focus</h3>
              <p class="text-gray-600">Building connections and fostering meaningful discussions</p>
            </div>
            <div class="text-center">
              <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full mb-4">
                <LightBulbIcon class="h-8 w-8 text-amber-600" />
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Innovation</h3>
              <p class="text-gray-600">Exploring new ideas and pushing boundaries in digital storytelling</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Mission Section -->
    <section class="py-20 lg:py-32">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-16 lg:items-center">
          <div>
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-8 leading-tight">
              Our Mission & Values
            </h2>
            <p class="text-lg text-gray-600 mb-8 leading-relaxed">
              Founded with a vision to democratize knowledge and foster meaningful connections, 
              we believe in the power of well-told stories to educate, inspire, and transform perspectives.
            </p>
            <div class="space-y-6">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full">
                    <CheckIcon class="h-5 w-5 text-blue-600" />
                  </div>
                </div>
                <div class="ml-4">
                  <h4 class="text-lg font-semibold text-gray-900 mb-2">Authenticity</h4>
                  <p class="text-gray-600">Every piece we publish is genuine, well-researched, and reflects real experiences</p>
                </div>
              </div>
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-full">
                    <CheckIcon class="h-5 w-5 text-indigo-600" />
                  </div>
                </div>
                <div class="ml-4">
                  <h4 class="text-lg font-semibold text-gray-900 mb-2">Accessibility</h4>
                  <p class="text-gray-600">We make complex topics understandable and accessible to everyone</p>
                </div>
              </div>
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="flex items-center justify-center w-8 h-8 bg-amber-100 rounded-full">
                    <CheckIcon class="h-5 w-5 text-amber-600" />
                  </div>
                </div>
                <div class="ml-4">
                  <h4 class="text-lg font-semibold text-gray-900 mb-2">Growth</h4>
                  <p class="text-gray-600">Continuous learning and improvement in everything we do</p>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-12 lg:mt-0">
            <div class="bg-white rounded-3xl shadow-2xl p-8 lg:p-12">
              <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full mb-6">
                  <HeartIcon class="h-10 w-10 text-white" />
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Impact</h3>
                <div class="grid grid-cols-2 gap-6 text-center">
                  <div>
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ stats.articles }}+</div>
                    <div class="text-sm text-gray-600">Articles Published</div>
                  </div>
                  <div>
                    <div class="text-3xl font-bold text-indigo-600 mb-2">{{ stats.readers }}+</div>
                    <div class="text-sm text-gray-600">Monthly Readers</div>
                  </div>
                  <div>
                    <div class="text-3xl font-bold text-amber-600 mb-2">{{ stats.countries }}+</div>
                    <div class="text-sm text-gray-600">Countries Reached</div>
                  </div>
                  <div>
                    <div class="text-3xl font-bold text-green-600 mb-2">{{ stats.years }}+</div>
                    <div class="text-sm text-gray-600">Years Experience</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Team Section -->
    <section class="bg-white py-20 lg:py-32">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
          <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-8">
            Meet Our Amazing Team
          </h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
            The passionate individuals behind every story, bringing diverse backgrounds, 
            expertise, and creativity to create exceptional content.
          </p>
        </div>

        <!-- Team Grid -->
        <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="member in teamMembers"
            :key="member.id"
            class="group text-center bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 p-8 transform hover:-translate-y-2"
          >
            <div class="relative mb-8">
              <div class="w-32 h-32 lg:w-40 lg:h-40 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center mx-auto shadow-lg group-hover:shadow-xl transition-all duration-300">
                <UserIcon class="w-16 h-16 lg:w-20 lg:h-20 text-white" />
              </div>
            </div>

            <div class="space-y-4">
              <div>
                <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">{{ member.name }}</h3>
                <p class="text-lg text-blue-600 font-semibold mb-3">{{ member.position }}</p>
              </div>

              <p class="text-gray-600 leading-relaxed">
                {{ member.bio }}
              </p>

              <!-- Specialties -->
              <div class="flex flex-wrap justify-center gap-2 mt-4">
                <span
                  v-for="specialty in member.specialties"
                  :key="specialty"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700"
                >
                  {{ specialty }}
                </span>
              </div>

              <!-- Stats -->
              <div class="grid grid-cols-2 gap-4 pt-6 border-t border-gray-100">
                <div class="text-center">
                  <div class="text-2xl font-bold text-blue-600">{{ member.stats.articles }}</div>
                  <div class="text-sm text-gray-500">Articles</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-indigo-600">{{ member.stats.experience }}</div>
                  <div class="text-sm text-gray-500">Years Exp.</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Join Our Team Section -->
    <section class="bg-gradient-to-r from-blue-600 to-indigo-600 py-20">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-8">
          Want to Join Our Team?
        </h2>
        <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto leading-relaxed">
          We're always looking for passionate writers, editors, and creative minds 
          who share our vision of creating meaningful content.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link
            to="/contact"
            class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-2xl hover:bg-gray-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
          >
            <EnvelopeIcon class="h-5 w-5 mr-3" />
            Get In Touch
          </router-link>
          <router-link
            to="/blog"
            class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-semibold rounded-2xl hover:bg-white hover:text-blue-600 transition-all duration-200"
          >
            <DocumentTextIcon class="h-5 w-5 mr-3" />
            Read Our Work
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import {
  PencilIcon,
  UsersIcon,
  LightBulbIcon,
  HeartIcon,
  CheckIcon,
  UserIcon,
  EnvelopeIcon,
  DocumentTextIcon
} from '@heroicons/vue/24/outline';

// Stats data
const stats = ref({
  articles: 150,
  readers: '25K',
  countries: 45,
  years: 5
});

// Team members data
const teamMembers = ref([
  {
    id: 1,
    name: 'Sarah Johnson',
    position: 'Editor in Chief',
    bio: 'With over 8 years in digital publishing, Sarah leads our editorial vision and ensures every piece meets our high standards for quality and authenticity.',
    specialties: ['Content Strategy', 'Digital Publishing', 'Team Leadership'],
    stats: {
      articles: 85,
      experience: 8
    }
  },
  {
    id: 2,
    name: 'Marcus Chen',
    position: 'Senior Writer',
    bio: 'Marcus brings technical expertise and creative storytelling together, specializing in technology, innovation, and digital transformation topics.',
    specialties: ['Technology', 'Innovation', 'Research'],
    stats: {
      articles: 67,
      experience: 6
    }
  },
  {
    id: 3,
    name: 'Emily Rodriguez',
    position: 'Creative Director',
    bio: 'Emily crafts the visual storytelling that brings our content to life, from photography to graphic design and user experience.',
    specialties: ['Visual Design', 'Photography', 'UX/UI'],
    stats: {
      articles: 42,
      experience: 5
    }
  },
  {
    id: 4,
    name: 'David Kim',
    position: 'Technical Lead',
    bio: 'David ensures our platform runs smoothly and efficiently, while also contributing insights on web development and digital tools.',
    specialties: ['Web Development', 'Performance', 'Security'],
    stats: {
      articles: 28,
      experience: 7
    }
  },
  {
    id: 5,
    name: 'Lisa Thompson',
    position: 'Community Manager',
    bio: 'Lisa builds and nurtures our community, engaging with readers and fostering meaningful discussions around our content.',
    specialties: ['Community Building', 'Social Media', 'Engagement'],
    stats: {
      articles: 35,
      experience: 4
    }
  },
  {
    id: 6,
    name: 'Alex Wilson',
    position: 'Research Analyst',
    bio: 'Alex dives deep into data and trends, providing the research foundation that makes our content insightful and well-informed.',
    specialties: ['Data Analysis', 'Market Research', 'Trends'],
    stats: {
      articles: 23,
      experience: 3
    }
  }
]);

// Methods
const getSocialIcon = (platform: string) => {
  // Return appropriate social media icons based on platform
  // This would need to be implemented with your icon library
  return 'div'; // placeholder
};

onMounted(() => {
  // Could load team data from API here
  console.log('About page loaded');
});
</script>

<style scoped>
/* Team card hover effects */
.group:hover .group-hover\:shadow-2xl {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Smooth transitions */
* {
  transition-property: transform, box-shadow, background-color, border-color, color, opacity;
  transition-duration: 300ms;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Stats counter animation (could be enhanced with actual counting) */
@keyframes countUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.stats-enter-active {
  animation: countUp 0.6s ease-out;
}
</style> 