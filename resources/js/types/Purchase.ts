import type { ApiResponse } from './api';
import type { Product } from './Product';

export interface Purchase {
  product: Product;
  points_deducted: number;
  remaining_balance: number;
}

export interface PurchaseResponse extends ApiResponse<Purchase> {}