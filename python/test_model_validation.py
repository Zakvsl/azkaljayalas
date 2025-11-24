import json
import subprocess
import sys

# Load test cases
with open('test_cases_2025.json', 'r') as f:
    test_cases = json.load(f)

print("=" * 80)
print("TESTING MODEL PREDICTIONS WITH YEAR-BASED PRICING")
print("=" * 80)

results = []
for i, test_case in enumerate(test_cases, 1):
    name = test_case.pop('name')
    expected = test_case.pop('expected')
    
    # Save to temp file
    temp_file = f'temp_test_{i}.json'
    with open(temp_file, 'w') as f:
        json.dump(test_case, f)
    
    # Run prediction
    try:
        result = subprocess.run(
            ['python', 'predict.py', temp_file],
            capture_output=True,
            text=True,
            timeout=10
        )
        
        if result.returncode == 0:
            response = json.loads(result.stdout)
            if response.get('success'):
                predicted = response['predicted_price']
                error = predicted - expected
                error_pct = (error / expected) * 100
                
                results.append({
                    'name': name,
                    'expected': expected,
                    'predicted': predicted,
                    'error': error,
                    'error_pct': error_pct
                })
                
                print(f"\n{i}. {name}")
                print(f"   Tahun: {test_case['tahun']} | Produk: {test_case['produk']} | Material: {test_case['jenis_material']}")
                print(f"   Ukuran: {test_case['ukuran']}m²")
                print(f"   Expected:  Rp {expected:>13,}")
                print(f"   Predicted: Rp {predicted:>13,.0f}")
                print(f"   Error:     Rp {error:>13,.0f} ({error_pct:+.2f}%)")
                
                if abs(error_pct) <= 10:
                    print(f"   Status: ✅ GOOD (within 10%)")
                elif abs(error_pct) <= 15:
                    print(f"   Status: ⚠️  ACCEPTABLE (within 15%)")
                else:
                    print(f"   Status: ❌ NEEDS IMPROVEMENT (>15% error)")
            else:
                print(f"\n{i}. {name}")
                print(f"   ❌ Prediction failed: {response.get('message')}")
        else:
            print(f"\n{i}. {name}")
            print(f"   ❌ Execution failed: {result.stderr}")
    
    except Exception as e:
        print(f"\n{i}. {name}")
        print(f"   ❌ Error: {e}")

# Summary
print("\n" + "=" * 80)
print("SUMMARY")
print("=" * 80)

if results:
    total_cases = len(results)
    good_cases = sum(1 for r in results if abs(r['error_pct']) <= 10)
    acceptable_cases = sum(1 for r in results if 10 < abs(r['error_pct']) <= 15)
    poor_cases = sum(1 for r in results if abs(r['error_pct']) > 15)
    
    avg_error_pct = sum(abs(r['error_pct']) for r in results) / total_cases
    max_error = max(results, key=lambda x: abs(x['error_pct']))
    min_error = min(results, key=lambda x: abs(x['error_pct']))
    
    print(f"\nTotal Test Cases: {total_cases}")
    print(f"✅ Good (<10% error):        {good_cases} ({good_cases/total_cases*100:.1f}%)")
    print(f"⚠️  Acceptable (10-15% error): {acceptable_cases} ({acceptable_cases/total_cases*100:.1f}%)")
    print(f"❌ Poor (>15% error):        {poor_cases} ({poor_cases/total_cases*100:.1f}%)")
    
    print(f"\nAverage Absolute Error: {avg_error_pct:.2f}%")
    print(f"Best Prediction:  {min_error['name']} ({abs(min_error['error_pct']):.2f}%)")
    print(f"Worst Prediction: {max_error['name']} ({abs(max_error['error_pct']):.2f}%)")
    
    print("\n" + "=" * 80)
