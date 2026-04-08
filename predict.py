import mysql.connector

def main():
    try:
        db = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="smart_canteen_db"
        )
        cursor = db.cursor(dictionary=True)

        # 1. Fetch avg volume
        query = "SELECT food_name, AVG(quantity) as avg_qty FROM orders GROUP BY food_name"
        cursor.execute(query)
        historical_data = cursor.fetchall()

        cursor.execute("TRUNCATE TABLE predictions")
        insert_query = "INSERT INTO predictions (food_name, predicted_qty, peak_hour, demand_category) VALUES (%s, %s, %s, %s)"
        
        for record in historical_data:
            food = record['food_name']
            
            # Predict demand volume based on 15 multiplier
            avg_amount = float(record['avg_qty'])
            predicted = avg_amount * 15 
            
            # --- NEW AI FEATURE 1: Smart Categorization ---
            if predicted >= 25:
                category = 'High Demand'
            elif predicted >= 10:
                category = 'Medium Demand'
            else:
                category = 'Low Demand'
                
            # --- NEW AI FEATURE 2: Time Series Peak Analysis ---
            # Identifies exactly what HOUR of the day this specific item is ordered most
            peak_sql = "SELECT HOUR(order_date) as hr, COUNT(*) as c FROM orders WHERE food_name=%s GROUP BY hr ORDER BY c DESC LIMIT 1"
            cursor.execute(peak_sql, (food,))
            peak_data = cursor.fetchone()
            
            if peak_data and peak_data['hr'] is not None:
                hr = peak_data['hr']
                # Create a readable time range (e.g. "14:00 to 15:00")
                peak_hour_str = f"{hr}:00 to {hr+1}:00"
            else:
                peak_hour_str = "Insufficient Data"

            cursor.execute(insert_query, (food, predicted, peak_hour_str, category))

        db.commit()
        print("Advanced ML Pipeline Executed: Volumes, Categories, and Logistics peaks successfully computed!")

    except Exception as e:
        print(f"Error: {e}")

    finally:
        if 'db' in locals() and db.is_connected():
            cursor.close()
            db.close()

if __name__ == "__main__":
    main()
