import type { ApiResponse } from './api';

export interface Card {
  card_id: number;
  card_number: string;
  points_balance: number;
  employee_name: string;
  daily_point_limit: number;
}

export interface CardVerificationResponse extends ApiResponse<Card> {}