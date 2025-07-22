import type { ApiResponse } from './api';

export interface PurchaseProduct {
  name: string;
  description: string;
  points_deducted: number;
}

export interface Purchase {
  product: PurchaseProduct;
  remaining_balance: number;
  transaction_id: string;
}

export interface PurchaseResponse extends ApiResponse<Purchase> {}