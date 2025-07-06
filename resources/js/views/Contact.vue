<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        <div class="text-center">
          <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 mb-8 leading-tight">
            Get In Touch
          </h1>
          <p class="text-xl lg:text-2xl text-gray-600 mb-12 max-w-3xl mx-auto leading-relaxed">
            Have a question, suggestion, or just want to say hello? 
            We'd love to hear from you. Let's start a conversation.
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section class="py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-12 lg:gap-16">
          <!-- Contact Information -->
          <div class="lg:col-span-5 mb-16 lg:mb-0">
            <div class="bg-white rounded-3xl shadow-xl p-8 lg:p-12">
              <h2 class="text-3xl font-bold text-gray-900 mb-8">Let's Connect</h2>
              
              <!-- Contact Methods -->
              <div class="space-y-8">
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl">
                      <EnvelopeIcon class="h-6 w-6 text-blue-600" />
                    </div>
                  </div>
                  <div class="ml-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Email Us</h3>
                    <p class="text-gray-600 mb-2">Send us an email and we'll get back to you within 24 hours</p>
                    <a href="mailto:hello@example.com" class="text-blue-600 hover:text-blue-700 font-medium">
                      hello@example.com
                    </a>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-xl">
                      <ChatBubbleLeftIcon class="h-6 w-6 text-indigo-600" />
                    </div>
                  </div>
                  <div class="ml-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Live Chat</h3>
                    <p class="text-gray-600 mb-2">Chat with us in real-time for quick questions</p>
                    <button 
                      @click="openLiveChat"
                      class="text-indigo-600 hover:text-indigo-700 font-medium"
                    >
                      Start a conversation
                    </button>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-amber-100 rounded-xl">
                      <MapPinIcon class="h-6 w-6 text-amber-600" />
                    </div>
                  </div>
                  <div class="ml-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Visit Us</h3>
                    <p class="text-gray-600">
                      123 Digital Street<br>
                      Innovation District<br>
                      Tech City, TC 12345
                    </p>
                  </div>
                </div>
              </div>

              <!-- Social Media -->
              <div class="pt-8 mt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Follow Us</h3>
                <div class="flex space-x-4">
                  <a
                    v-for="(link, platform) in socialLinks"
                    :key="platform"
                    :href="link"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-blue-100 hover:text-blue-600 transition-colors"
                  >
                    <component :is="getSocialIcon(platform)" class="w-5 h-5" />
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Contact Form -->
          <div class="lg:col-span-7">
            <div class="bg-white rounded-3xl shadow-xl p-8 lg:p-12">
              <h2 class="text-3xl font-bold text-gray-900 mb-8">Send Us a Message</h2>
              
              <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Name and Email Row -->
                <div class="grid gap-6 md:grid-cols-2">
                  <div>
                    <label for="firstName" class="block text-sm font-semibold text-gray-900 mb-3">
                      First Name *
                    </label>
                    <input
                      id="firstName"
                      v-model="form.firstName"
                      type="text"
                      required
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                      placeholder="Enter your first name"
                    />
                  </div>
                  
                  <div>
                    <label for="lastName" class="block text-sm font-semibold text-gray-900 mb-3">
                      Last Name *
                    </label>
                    <input
                      id="lastName"
                      v-model="form.lastName"
                      type="text"
                      required
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                      placeholder="Enter your last name"
                    />
                  </div>
                </div>

                <!-- Email -->
                <div>
                  <label for="email" class="block text-sm font-semibold text-gray-900 mb-3">
                    Email Address *
                  </label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="your.email@example.com"
                  />
                </div>

                <!-- Subject -->
                <div>
                  <label for="subject" class="block text-sm font-semibold text-gray-900 mb-3">
                    Subject *
                  </label>
                  <select
                    id="subject"
                    v-model="form.subject"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                  >
                    <option value="">Select a subject</option>
                    <option value="general">General Inquiry</option>
                    <option value="collaboration">Collaboration</option>
                    <option value="feedback">Feedback</option>
                    <option value="technical">Technical Support</option>
                    <option value="press">Press & Media</option>
                    <option value="other">Other</option>
                  </select>
                </div>

                <!-- Message -->
                <div>
                  <label for="message" class="block text-sm font-semibold text-gray-900 mb-3">
                    Message *
                  </label>
                  <textarea
                    id="message"
                    v-model="form.message"
                    rows="6"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                    placeholder="Tell us how we can help you..."
                  ></textarea>
                  <p class="mt-2 text-sm text-gray-500">{{ form.message.length }}/1000 characters</p>
                </div>

                <!-- Newsletter Subscription -->
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="newsletter"
                      v-model="form.newsletter"
                      type="checkbox"
                      class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                  </div>
                  <div class="ml-3">
                    <label for="newsletter" class="text-sm text-gray-600">
                      I'd like to receive updates about new articles and features
                    </label>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6">
                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    :class="[
                      'w-full flex items-center justify-center px-8 py-4 text-lg font-semibold rounded-2xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1',
                      isSubmitting 
                        ? 'bg-gray-400 cursor-not-allowed' 
                        : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white'
                    ]"
                  >
                    <span v-if="!isSubmitting" class="flex items-center">
                      <PaperAirplaneIcon class="h-5 w-5 mr-3" />
                      Send Message
                    </span>
                    <span v-else class="flex items-center">
                      <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.25"/>
                        <path fill="currentColor" opacity="0.75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                      </svg>
                      Sending...
                    </span>
                  </button>
                </div>
              </form>

              <!-- Success Message -->
              <div
                v-if="showSuccess"
                class="mt-6 p-4 bg-green-50 border border-green-200 rounded-xl"
              >
                <div class="flex items-center">
                  <CheckCircleIcon class="h-5 w-5 text-green-600 mr-3" />
                  <p class="text-green-800 font-medium">
                    Thank you! Your message has been sent successfully. We'll get back to you soon.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import {
  EnvelopeIcon,
  ChatBubbleLeftIcon,
  MapPinIcon,
  PaperAirplaneIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline';

// Form data
const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  subject: '',
  message: '',
  newsletter: false
});

// Form state
const isSubmitting = ref(false);
const showSuccess = ref(false);
const errors = ref<Record<string, string>>({});

// Social media links
const socialLinks = {
  twitter: '#',
  linkedin: '#',
  instagram: '#',
  facebook: '#'
};

// Methods
const validateForm = () => {
  errors.value = {};

  if (!form.firstName.trim()) {
    errors.value.firstName = 'First name is required';
  }

  if (!form.lastName.trim()) {
    errors.value.lastName = 'Last name is required';
  }

  if (!form.email.trim()) {
    errors.value.email = 'Email is required';
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.value.email = 'Please enter a valid email address';
  }

  if (!form.subject) {
    errors.value.subject = 'Please select a subject';
  }

  if (!form.message.trim()) {
    errors.value.message = 'Message is required';
  } else if (form.message.length < 10) {
    errors.value.message = 'Message must be at least 10 characters';
  } else if (form.message.length > 1000) {
    errors.value.message = 'Message must be less than 1000 characters';
  }

  return Object.keys(errors.value).length === 0;
};

const submitForm = async () => {
  if (!validateForm()) {
    return;
  }

  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // In a real app, you would send the form data to your API
    console.log('Form submitted:', form);
    
    // Reset form
    Object.assign(form, {
      firstName: '',
      lastName: '',
      email: '',
      subject: '',
      message: '',
      newsletter: false
    });

    showSuccess.value = true;

    // Hide success message after 5 seconds
    setTimeout(() => {
      showSuccess.value = false;
    }, 5000);

  } catch (error) {
    console.error('Error submitting form:', error);
    // Handle error (show error message)
  } finally {
    isSubmitting.value = false;
  }
};

const openLiveChat = () => {
  // Implement live chat functionality
  console.log('Opening live chat...');
};

const getSocialIcon = (platform: string) => {
  // Return appropriate social media icons based on platform
  // This would need to be implemented with your icon library
  return 'div'; // placeholder
};
</script>

<style scoped>
/* Custom input focus effects */
input:focus,
select:focus,
textarea:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Smooth transitions */
* {
  transition-property: all;
  transition-duration: 200ms;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Form validation animations */
.error-enter-active,
.error-leave-active {
  transition: all 0.3s ease;
}

.error-enter-from,
.error-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Submit button hover effects */
button:not(:disabled):hover {
  transform: translateY(-2px);
}

/* Loading spinner */
@keyframes spin {
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style> 