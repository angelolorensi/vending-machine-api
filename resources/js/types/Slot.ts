import type { Product } from './Product';

export interface Slot {
  slot_id: number;
  number: string;
  product: Product | null;
  quantity: number;
  machine_id: number;
}
