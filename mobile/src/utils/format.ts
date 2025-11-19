/**
 * Formatting utilities
 */

/**
 * Format date to French locale
 */
export const formatDate = (dateString: string, format: 'short' | 'long' | 'full' = 'short'): string => {
  const date = new Date(dateString);

  if (format === 'short') {
    return date.toLocaleDateString('fr-FR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    });
  }

  if (format === 'long') {
    return date.toLocaleDateString('fr-FR', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    });
  }

  // full format
  return date.toLocaleDateString('fr-FR', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
};

/**
 * Format time
 */
export const formatTime = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit',
  });
};

/**
 * Format date and time
 */
export const formatDateTime = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

/**
 * Format currency (TND)
 */
export const formatCurrency = (amount: number): string => {
  return `${amount.toFixed(2)} TND`;
};

/**
 * Format phone number
 */
export const formatPhone = (phone: string): string => {
  // Format: +216 XX XXX XXX
  const cleaned = phone.replace(/\D/g, '');
  if (cleaned.startsWith('216')) {
    const number = cleaned.substring(3);
    return `+216 ${number.substring(0, 2)} ${number.substring(2, 5)} ${number.substring(5)}`;
  }
  if (cleaned.length === 8) {
    return `+216 ${cleaned.substring(0, 2)} ${cleaned.substring(2, 5)} ${cleaned.substring(5)}`;
  }
  return phone;
};

/**
 * Format distance
 */
export const formatDistance = (distance: number): string => {
  if (distance < 1) {
    return `${Math.round(distance * 1000)} m`;
  }
  return `${distance.toFixed(1)} km`;
};

/**
 * Format duration (seconds to MM:SS)
 */
export const formatDuration = (seconds: number): string => {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
};

/**
 * Format relative time (e.g., "il y a 2 heures")
 */
export const formatRelativeTime = (dateString: string): string => {
  const date = new Date(dateString);
  const now = new Date();
  const diffMs = now.getTime() - date.getTime();
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMs / 3600000);
  const diffDays = Math.floor(diffMs / 86400000);

  if (diffMins < 1) {
    return 'À l\'instant';
  }
  if (diffMins < 60) {
    return `Il y a ${diffMins} minute${diffMins > 1 ? 's' : ''}`;
  }
  if (diffHours < 24) {
    return `Il y a ${diffHours} heure${diffHours > 1 ? 's' : ''}`;
  }
  if (diffDays < 7) {
    return `Il y a ${diffDays} jour${diffDays > 1 ? 's' : ''}`;
  }
  return formatDate(dateString);
};

/**
 * Truncate text
 */
export const truncate = (text: string, maxLength: number): string => {
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
};

/**
 * Capitalize first letter
 */
export const capitalize = (text: string): string => {
  return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
};

/**
 * Format name (Dr. Prénom Nom)
 */
export const formatDoctorName = (prenom: string, nom: string): string => {
  return `Dr. ${capitalize(prenom)} ${nom.toUpperCase()}`;
};
