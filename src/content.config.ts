import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';

const productos = defineCollection({
  loader: glob({ pattern: '**/*.json', base: './src/content/productos' }),
  schema: z.object({
    nombre: z.string(),
    referencia: z.string(),
    precio: z.number().default(0),
    descripcionCorta: z.string().optional(),
    descripcionLarga: z.string().optional(),
    imagenPrincipal: z.string(),
    imagenes: z.array(z.string()).default([]),
    destacado: z.boolean().default(false),
    ordenDestacado: z.number().default(0),
    activo: z.boolean().default(true),
    categorias: z.array(z.string()).default([]),
    tallasDisponibles: z.array(z.string()).default(['35','36','37','38','39','40']),
    badges: z.array(z.enum(['destacado','nuevo','favoritoClientas','ultimasUnidades'])).default([]),
    mensajeWhatsapp: z.string().optional(),
  }),
});

export const collections = { productos };
