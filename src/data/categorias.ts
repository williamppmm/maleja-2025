export interface Categoria {
  slug: string;
  nombre: string;
  padre?: string;
}

export const categorias: Categoria[] = [
  { slug: 'sandalia',        nombre: 'Sandalia' },
  { slug: 'tacon',           nombre: 'Tacón' },
  { slug: 'plataforma',      nombre: 'Plataforma' },
  { slug: 'deportiva',       nombre: 'Deportiva' },
  { slug: 'canoa',           nombre: 'Canoa / Suela confort' },
];

export const whatsappMessages = {
  home:      'Hola Maleja, quiero conocer el catálogo',
  catalogo:  'Hola Maleja, quiero asesoría para elegir mi calzado',
  contacto:  'Hola Maleja, quiero que me asesores para encontrar mi calzado perfecto',
  producto:  (nombre: string, referencia: string, talla?: string) =>
    talla
      ? `Hola Maleja, me interesa ${nombre}, ref ${referencia}, talla ${talla}`
      : `Hola Maleja, me interesa ${nombre}, ref ${referencia}`,
} as const;

export const WHATSAPP_NUMBER = '573172703742';
