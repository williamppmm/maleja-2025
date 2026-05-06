export const SITE_URL = 'https://www.malejacalzado.com';
export const BRAND_NAME = 'MALEJA Calzado';
export const PHONE_DISPLAY = '+57 317 270 3742';
export const PHONE_SCHEMA = '+573172703742';
export const EMAIL = 'ventas@malejacalzado.com';
export const LOGO_URL = `${SITE_URL}/images/logos/logo.svg`;
export const DEFAULT_OG_IMAGE = `${SITE_URL}/images/og-default.jpg`;
export const INSTAGRAM_URL = 'https://www.instagram.com/malejacalzado/';
export const FACEBOOK_URL = 'https://www.facebook.com/people/Maleja-Calzado/61578936597273/';

export const localBusinessJsonLd = {
  '@context': 'https://schema.org',
  '@type': 'LocalBusiness',
  '@id': `${SITE_URL}/#localbusiness`,
  name: BRAND_NAME,
  image: DEFAULT_OG_IMAGE,
  logo: LOGO_URL,
  url: SITE_URL,
  telephone: PHONE_SCHEMA,
  email: EMAIL,
  address: {
    '@type': 'PostalAddress',
    addressLocality: 'Cali',
    addressRegion: 'Valle del Cauca',
    addressCountry: 'CO',
  },
  areaServed: {
    '@type': 'City',
    name: 'Cali',
    address: {
      '@type': 'PostalAddress',
      addressCountry: 'CO',
      addressRegion: 'Valle del Cauca',
    },
  },
  openingHours: 'Mo-Sa 08:00-18:00',
  sameAs: [INSTAGRAM_URL, FACEBOOK_URL],
} as const;
