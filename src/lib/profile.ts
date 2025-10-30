'use client';

export interface UserProfile {
  name: string;
  avatarUrl: string;
}

const PROFILE_KEY = 'vega_user_profile';

// Return a default profile if none is stored
const defaultProfile: UserProfile = {
  name: 'Guest User',
  avatarUrl: '',
};

export function getUserProfile(): UserProfile {
  if (typeof window === 'undefined') {
    return defaultProfile;
  }
  const profileJson = localStorage.getItem(PROFILE_KEY);
  if (!profileJson) {
    return defaultProfile;
  }
  try {
    const profile = JSON.parse(profileJson);
    // Basic validation to ensure the stored data matches the expected format
    if (typeof profile.name === 'string' && typeof profile.avatarUrl === 'string') {
      return profile;
    }
  } catch (error) {
    console.error('Failed to parse user profile from localStorage', error);
  }
  return defaultProfile;
}

export function saveUserProfile(profile: Partial<UserProfile>): void {
  if (typeof window === 'undefined') return;
  const existingProfile = getUserProfile();
  const newProfile = { ...existingProfile, ...profile };
  localStorage.setItem(PROFILE_KEY, JSON.stringify(newProfile));
}
